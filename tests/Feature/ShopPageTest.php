<?php

beforeEach(function () {
    seedStorefrontCatalog();
});

it('renders the shop page with paginated products', function () {
    $response = $this->get(route('shop'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Shop/Index')
        ->has('products.data', 8)
        ->where('products.meta.current_page', 1)
        ->where('products.meta.last_page', 3)
        ->where('products.meta.per_page', 8)
        ->where('products.meta.total', 20)
        ->where('products.data.0.slug', 'mens-fashion-tee')
        ->where('products.data.0.name', "Men's Fashion T Shirt")
        ->where('products.data.0.price', '$139.00')
        ->where('products.data.0.image', '/img/shop/1.jpg')
        ->where('products.data.0.rating', 5)
    );
});

it('renders the requested shop page', function () {
    $response = $this->get(route('shop', ['page' => 2]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('products.data', 8)
        ->where('products.meta.current_page', 2)
    );
});

it('renders the final shop page with remaining products', function () {
    $response = $this->get(route('shop', ['page' => 3]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('products.data', 4)
        ->where('products.meta.current_page', 3)
        ->where('products.meta.last_page', 3)
    );
});
