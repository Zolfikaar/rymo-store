<?php

beforeEach(function () {
    seedStorefrontCatalog();
});

it('renders the home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Home')
        ->has('featuredProducts', 4)
        ->has('clothesProducts', 4)
        ->has('watchesProducts', 4)
        ->has('shoesProducts', 4)
        ->where('featuredProducts.0.slug', 'mens-fashion-tee')
    );
});
