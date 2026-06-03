<?php

beforeEach(function () {
    seedStorefrontCatalog();
});

it('renders the shop page with products', function () {
    $response = $this->get(route('shop'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Shop/Index')
        ->has('products', 20)
        ->where('products.0.slug', 'mens-fashion-tee')
        ->where('products.0.name', "Men's Fashion T Shirt")
        ->where('products.0.price', '$139.00')
        ->where('products.0.image', '/img/shop/1.jpg')
        ->where('products.0.rating', 5)
    );
});
