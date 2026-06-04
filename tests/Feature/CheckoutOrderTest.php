<?php

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;

beforeEach(function () {
    seedStorefrontCatalog();
});

function validCustomerInfo(): array
{
    return [
        'name' => 'Jane Guest',
        'phone' => '+1 555-0100',
        'address' => '123 Main Street, Springfield, US',
    ];
}

it('allows guests to place an order without authentication', function () {
    $product = Product::query()->where('slug', 'mens-fashion-tee')->firstOrFail();

    $response = $this->post(route('checkout.store'), [
        'customer_info' => validCustomerInfo(),
        'cart_items' => [
            ['id' => $product->id, 'quantity' => 1],
        ],
    ]);

    $response->assertRedirect(route('cart'));
    $response->assertSessionHas(
        'success',
        'Your order has been placed successfully and is pending review.',
    );
});

it('places a guest order using server-side product prices', function () {
    $product = Product::query()->where('slug', 'mens-fashion-tee')->firstOrFail();

    $this->post(route('checkout.store'), [
        'customer_info' => validCustomerInfo(),
        'cart_items' => [
            [
                'id' => $product->id,
                'quantity' => 2,
                'price' => 1.00,
            ],
        ],
    ])->assertRedirect();

    $order = Order::query()->first();

    expect($order)->not->toBeNull()
        ->and($order->user_id)->toBeNull()
        ->and($order->customer_name)->toBe('Jane Guest')
        ->and($order->customer_phone)->toBe('+1 555-0100')
        ->and($order->shipping_address)->toBe('123 Main Street, Springfield, US')
        ->and($order->status)->toBe('pending')
        ->and((float) $order->total_price)->toBe((float) $product->price * 2);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price_at_purchase' => $product->price,
    ]);

    $cart = Cart::query()->first();

    expect($cart)->not->toBeNull()
        ->and($cart->user_id)->toBeNull()
        ->and($cart->order_id)->toBe($order->id);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price_at_addition' => $product->price,
    ]);
});

it('requires customer information for checkout', function () {
    $product = Product::query()->where('slug', 'mens-fashion-tee')->firstOrFail();

    $this->post(route('checkout.store'), [
        'customer_info' => [
            'name' => '',
            'phone' => '',
            'address' => '',
        ],
        'cart_items' => [
            ['id' => $product->id, 'quantity' => 1],
        ],
    ])->assertSessionHasErrors([
        'customer_info.name',
        'customer_info.phone',
        'customer_info.address',
    ]);
});

it('rejects checkout when a product does not exist', function () {
    $this->post(route('checkout.store'), [
        'customer_info' => validCustomerInfo(),
        'cart_items' => [
            ['id' => 999999, 'quantity' => 1],
        ],
    ])->assertSessionHasErrors('cart_items');
});

it('rejects checkout with an empty cart payload', function () {
    $this->post(route('checkout.store'), [
        'customer_info' => validCustomerInfo(),
        'cart_items' => [],
    ])->assertSessionHasErrors('cart_items');
});
