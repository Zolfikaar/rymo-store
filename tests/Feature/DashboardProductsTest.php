<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedStorefrontCatalog();
    Storage::fake('public');
});

it('renders products catalog page for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.products.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard/Products')
        ->has('products.data')
        ->has('categories.data')
        ->has('brands.data')
        ->has('sizeOptions')
    );
});

it('creates products with uploaded images and sizes', function () {
    $user = User::factory()->create();
    $category = Category::query()->firstOrFail();
    $brand = Brand::query()->firstOrFail();

    $mainImage = UploadedFile::fake()->create('main.jpg', 100, 'image/jpeg');
    $galleryImage = UploadedFile::fake()->create('gallery.jpg', 100, 'image/jpeg');

    $this->actingAs($user)
        ->post(route('dashboard.products.store'), [
            'name' => 'Trail Cap',
            'slug' => 'trail-cap',
            'description' => 'Lightweight running cap',
            'price' => 29.99,
            'stock' => 15,
            'sku' => 'RYMO-CAP-999',
            'color' => 'Black',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'image' => $mainImage,
            'gallery_uploads' => [$galleryImage],
            'available_sizes' => ['S', 'M', 'L'],
        ])
        ->assertRedirect();

    $product = Product::query()->where('slug', 'trail-cap')->first();

    expect($product)->not->toBeNull()
        ->and($product->category_id)->toBe($category->id)
        ->and($product->brand_id)->toBe($brand->id)
        ->and($product->image_url)->toStartWith('/storage/products/')
        ->and($product->gallery_images)->toHaveCount(1)
        ->and($product->gallery_images[0])->toStartWith('/storage/products/')
        ->and($product->available_sizes)->toBe(['S', 'M', 'L']);
});

it('updates products and preserves existing gallery images', function () {
    $user = User::factory()->create();
    $product = Product::query()->firstOrFail();

    $this->actingAs($user)
        ->put(route('dashboard.products.update', $product->id), [
            'name' => 'Updated Product Name',
            'slug' => $product->slug,
            'description' => 'Updated description',
            'price' => 199.99,
            'stock' => 5,
            'sku' => $product->sku,
            'color' => 'Blue',
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'existing_gallery_images' => $product->gallery_images ?? [],
            'available_sizes' => ['XL'],
        ])
        ->assertRedirect();

    expect($product->fresh())
        ->name->toBe('Updated Product Name')
        ->price->toBe('199.99')
        ->available_sizes->toBe(['XL']);
});

it('prevents deleting products referenced by orders', function () {
    $user = User::factory()->create();
    $product = Product::query()->firstOrFail();

    $order = Order::query()->create([
        'customer_name' => 'Guest Buyer',
        'customer_phone' => '+1 555-0199',
        'total_price' => $product->price,
        'status' => 'pending',
        'shipping_address' => '456 Oak Avenue',
        'payment_method' => 'COD',
    ]);

    OrderItem::query()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => $product->price,
    ]);

    $this->actingAs($user)
        ->delete(route('dashboard.products.destroy', $product->id))
        ->assertSessionHasErrors('product');

    expect(Product::query()->whereKey($product->id)->exists())->toBeTrue();
});

it('deletes products without order history', function () {
    $user = User::factory()->create();

    $product = Product::query()->create([
        'name' => 'Disposable Item',
        'slug' => 'disposable-item',
        'description' => 'Temporary product',
        'price' => 9.99,
        'stock' => 1,
        'sku' => 'TEMP-001',
        'category_id' => Category::query()->value('id'),
        'brand_id' => Brand::query()->value('id'),
    ]);

    $this->actingAs($user)
        ->delete(route('dashboard.products.destroy', $product->id))
        ->assertRedirect();

    expect(Product::query()->whereKey($product->id)->exists())->toBeFalse();
});

it('validates product payloads', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('dashboard.products.store'), [
            'name' => '',
            'price' => -1,
            'stock' => -5,
            'sku' => '',
        ])
        ->assertSessionHasErrors(['name', 'price', 'stock', 'sku']);
});

it('requires authentication for product management routes', function () {
    $product = Product::query()->firstOrFail();

    $this->get(route('dashboard.products.index'))->assertRedirect(route('login'));
    $this->post(route('dashboard.products.store'), [])->assertRedirect(route('login'));
    $this->delete(route('dashboard.products.destroy', $product->id))->assertRedirect(route('login'));
});
