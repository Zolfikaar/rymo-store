<?php

it('renders the product detail page', function () {
    $response = $this->get(route('shop.product', 1));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Shop/Show')
        ->has('product')
        ->has('relatedProducts', 4)
        ->where('product.name', "Men's Fashion T Shirt")
    );
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
    $response->assertInertia(fn ($page) => $page
        ->component('Cart')
        ->has('items', 3)
    );
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
