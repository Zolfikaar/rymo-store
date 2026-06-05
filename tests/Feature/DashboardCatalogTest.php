<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    seedStorefrontCatalog();
});

it('renders categories catalog page for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.categories.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard/Catalog')
        ->where('activeTab', 'categories')
        ->has('categories.data')
        ->has('brands.data')
    );
});

it('renders brands catalog page for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.brands.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard/Catalog')
        ->where('activeTab', 'brands')
    );
});

it('creates and updates categories', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('dashboard.categories.store'), [
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => 'Bags and belts',
            'is_active' => true,
        ])
        ->assertRedirect();

    $category = Category::query()->where('slug', 'accessories')->first();
    expect($category)->not->toBeNull()
        ->and($category->name)->toBe('Accessories');

    $this->actingAs($user)
        ->put(route('dashboard.categories.update', $category), [
            'name' => 'Premium Accessories',
            'slug' => 'premium-accessories',
            'description' => 'Updated description',
            'is_active' => false,
        ])
        ->assertRedirect();

    expect($category->fresh())
        ->name->toBe('Premium Accessories')
        ->slug->toBe('premium-accessories')
        ->is_active->toBeFalse();
});

it('creates and updates brands', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('dashboard.brands.store'), [
            'name' => 'Nova',
            'slug' => 'nova',
        ])
        ->assertRedirect();

    $brand = Brand::query()->where('slug', 'nova')->first();
    expect($brand)->not->toBeNull();

    $this->actingAs($user)
        ->put(route('dashboard.brands.update', $brand), [
            'name' => 'Nova Sport',
            'slug' => 'nova-sport',
        ])
        ->assertRedirect();

    expect($brand->fresh())
        ->name->toBe('Nova Sport')
        ->slug->toBe('nova-sport');
});

it('prevents deleting categories with products', function () {
    $user = User::factory()->create();
    $category = Category::query()->whereHas('products')->firstOrFail();

    $this->actingAs($user)
        ->delete(route('dashboard.categories.destroy', $category))
        ->assertSessionHasErrors('category');

    expect(Category::query()->whereKey($category->id)->exists())->toBeTrue();
});

it('prevents deleting brands with products', function () {
    $user = User::factory()->create();
    $brand = Brand::query()->whereHas('products')->firstOrFail();

    $this->actingAs($user)
        ->delete(route('dashboard.brands.destroy', $brand))
        ->assertSessionHasErrors('brand');

    expect(Brand::query()->whereKey($brand->id)->exists())->toBeTrue();
});

it('deletes empty categories and brands', function () {
    $user = User::factory()->create();

    $category = Category::query()->create([
        'name' => 'Temporary',
        'slug' => 'temporary',
        'is_active' => true,
    ]);

    $brand = Brand::query()->create([
        'name' => 'Temporary Brand',
        'slug' => 'temporary-brand',
    ]);

    $this->actingAs($user)
        ->delete(route('dashboard.categories.destroy', $category))
        ->assertRedirect();

    $this->actingAs($user)
        ->delete(route('dashboard.brands.destroy', $brand))
        ->assertRedirect();

    expect(Category::query()->whereKey($category->id)->exists())->toBeFalse();
    expect(Brand::query()->whereKey($brand->id)->exists())->toBeFalse();
});

it('requires authentication for catalog management routes', function () {
    $category = Category::query()->firstOrFail();
    $brand = Brand::query()->firstOrFail();

    $this->get(route('dashboard.categories.index'))->assertRedirect(route('login'));
    $this->get(route('dashboard.brands.index'))->assertRedirect(route('login'));
    $this->post(route('dashboard.categories.store'), ['name' => 'Test'])->assertRedirect(route('login'));
    $this->delete(route('dashboard.categories.destroy', $category))->assertRedirect(route('login'));
    $this->delete(route('dashboard.brands.destroy', $brand))->assertRedirect(route('login'));
});
