<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    seedStorefrontCatalog();
});

function createDashboardOrder(array $overrides = [], array $items = []): Order
{
    $product = Product::query()->firstOrFail();

    $order = Order::query()->create(array_merge([
        'customer_name' => 'Jane Guest',
        'customer_phone' => '+1 555-0100',
        'total_price' => $product->price,
        'status' => 'pending',
        'shipping_address' => '123 Main Street',
        'payment_method' => 'COD',
    ], $overrides));

    if ($items === []) {
        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price_at_purchase' => $product->price,
        ]);
    } else {
        foreach ($items as $item) {
            OrderItem::query()->create(array_merge([
                'order_id' => $order->id,
            ], $item));
        }
    }

    return $order->fresh(['items.product']);
}

it('renders dashboard with orders and stats for authenticated users', function () {
    $user = User::factory()->create();
    createDashboardOrder(['status' => 'pending', 'total_price' => 100.00]);
    createDashboardOrder(['status' => 'completed', 'total_price' => 250.50]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->has('orders.data', 2)
        ->where('stats.pending_orders_count', 1)
        ->where('stats.total_sales', '250.50')
        ->where('stats.total_products', Product::query()->count())
        ->has('orders.data.0.items')
    );
});

it('updates order status via patch request', function () {
    $user = User::factory()->create();
    $order = createDashboardOrder(['status' => 'pending']);

    $response = $this->actingAs($user)->patch(route('dashboard.orders.update-status', $order), [
        'status' => 'completed',
    ]);

    $response->assertRedirect();
    expect($order->fresh()->status)->toBe('completed');
});

it('rejects invalid order status updates', function () {
    $user = User::factory()->create();
    $order = createDashboardOrder(['status' => 'pending']);

    $this->actingAs($user)
        ->patch(route('dashboard.orders.update-status', $order), [
            'status' => 'shipped',
        ])
        ->assertSessionHasErrors('status');
});

it('requires authentication to update order status', function () {
    $order = createDashboardOrder();

    $this->patch(route('dashboard.orders.update-status', $order), [
        'status' => 'completed',
    ])->assertRedirect(route('login'));
});
