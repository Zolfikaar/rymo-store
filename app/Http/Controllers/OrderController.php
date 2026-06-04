<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * @param  array<int, array<string, mixed>>  $cartItems
     * @return array<int, array{product_id: int, quantity: int}>
     */
    private function normalizeCartItems(array $cartItems): array
    {
        return collect($cartItems)
            ->map(function (array $item): array {
                $productId = $item['product_id'] ?? $item['id'] ?? null;

                if (! is_numeric($productId)) {
                    throw ValidationException::withMessages([
                        'cart_items' => ['Each cart item must include a valid product identifier.'],
                    ]);
                }

                return [
                    'product_id' => (int) $productId,
                    'quantity' => (int) $item['quantity'],
                ];
            })
            ->values()
            ->all();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_info' => ['required', 'array'],
            'customer_info.name' => ['required', 'string', 'max:255'],
            'customer_info.phone' => ['required', 'string', 'max:50'],
            'customer_info.address' => ['required', 'string', 'max:500'],
            'cart_items' => ['required', 'array', 'min:1'],
            'cart_items.*.quantity' => ['required', 'integer', 'min:1'],
            'cart_items.*.product_id' => ['sometimes', 'integer'],
            'cart_items.*.id' => ['sometimes', 'integer'],
            'payment_method' => ['nullable', 'string', 'max:50'],
        ]);

        $normalizedItems = $this->normalizeCartItems($validated['cart_items']);
        $customerInfo = $validated['customer_info'];

        DB::transaction(function () use ($request, $normalizedItems, $customerInfo, $validated): void {
            $productIds = collect($normalizedItems)->pluck('product_id')->unique()->values();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            if ($products->count() !== $productIds->count()) {
                throw ValidationException::withMessages([
                    'cart_items' => ['One or more products in your cart are no longer available.'],
                ]);
            }

            $totalPrice = 0.0;
            $lineItems = [];

            foreach ($normalizedItems as $item) {
                $product = $products->get($item['product_id']);

                if ($product === null) {
                    throw ValidationException::withMessages([
                        'cart_items' => ['One or more products in your cart are no longer available.'],
                    ]);
                }

                $priceAtPurchase = (float) $product->price;
                $totalPrice += $priceAtPurchase * $item['quantity'];

                $lineItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $priceAtPurchase,
                ];
            }

            $order = Order::query()->create([
                'user_id' => $request->user()?->id,
                'customer_name' => $customerInfo['name'],
                'customer_phone' => $customerInfo['phone'],
                'total_price' => round($totalPrice, 2),
                'status' => 'pending',
                'shipping_address' => $customerInfo['address'],
                'payment_method' => $validated['payment_method'] ?? 'COD',
            ]);

            $order->items()->createMany($lineItems);

            $cart = Cart::query()->create([
                'user_id' => $request->user()?->id,
                'order_id' => $order->id,
            ]);

            $cart->items()->createMany(
                collect($lineItems)->map(fn (array $lineItem): array => [
                    'product_id' => $lineItem['product_id'],
                    'quantity' => $lineItem['quantity'],
                    'price_at_addition' => $lineItem['price_at_purchase'],
                ])->all(),
            );
        });

        return redirect()
            ->route('cart')
            ->with('success', 'Your order has been placed successfully and is pending review.');
    }
}
