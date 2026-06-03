<?php

beforeEach(function () {
    seedStorefrontCatalog();
});

it('renders the product detail page', function () {
    $response = $this->get(route('shop.product', 'mens-fashion-tee'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Shop/Show')
        ->has('product')
        ->has('relatedProducts')
        ->where('product.slug', 'mens-fashion-tee')
        ->where('product.name', "Men's Fashion T Shirt")
        ->where('product.price', '$139.00')
        ->where('product.images', [
            '/img/shop/1.jpg',
            '/img/shop/24.jpg',
            '/img/shop/25.jpg',
            '/img/shop/26.jpg',
        ])
    );
});

it('does not leak gallery images for products without extras', function () {
    $response = $this->get(route('shop.product', 'alpine-trail-boots'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('product.slug', 'alpine-trail-boots')
        ->where('product.images', ['/img/shop/2.jpg'])
    );
});

it('returns 404 for unknown product slugs', function () {
    $this->get(route('shop.product', 'non-existent-product'))->assertNotFound();
});

it('renders the blog page', function () {
    $response = $this->get(route('blog'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Blog/Index')
        ->has('posts', 8)
    );
});

it('renders the cart page', function () {
    $response = $this->get(route('cart'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Cart'));
});

it('renders the contact page', function () {
    $response = $this->get(route('contact'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Contact'));
});

it('accepts contact form submissions', function () {
    $response = $this->post(route('contact.submit'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Order inquiry',
        'message' => 'I have a question about my order.',
    ]);

    $response->assertRedirect(route('contact'));
    $response->assertSessionHas('success');
});
