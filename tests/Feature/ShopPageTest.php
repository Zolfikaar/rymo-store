<?php

it('renders the shop page with products', function () {
    $response = $this->get(route('shop'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Shop/Index')
        ->has('products', 20)
        ->where('products.0.name', 'Sport Boots')
        ->where('products.0.price', '$92.00')
        ->where('products.0.image', '/img/shop/1.jpg')
        ->where('products.0.rating', 5)
    );
});
