<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [ShopController::class, 'home'])->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop');

Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.product');

Route::get('/blog', function () {
    $posts = [
        ['id' => 1, 'image' => '/img/blog/1.jpg', 'title' => 'The best way to change your summer wardrobe into autumn wardrobe', 'date' => 'Jan 11, 2021', 'size' => 'large'],
        ['id' => 2, 'image' => '/img/blog/2.jpg', 'title' => 'The best way to change your summer wardrobe into autumn wardrobe', 'date' => 'Jan 11, 2021', 'size' => 'large'],
        ['id' => 3, 'image' => '/img/blog/3.jpg', 'title' => "Men's fashion in leather", 'date' => 'Jan 11, 2021', 'size' => 'large'],
        ['id' => 4, 'image' => '/img/blog/4.jpg', 'title' => "DIYer and TV host Trisha Hershberger's journey through gaming keeps evolving", 'date' => 'Jan 11, 2021', 'size' => 'large'],
        ['id' => 5, 'image' => '/img/blog/banner.webp', 'title' => '', 'size' => 'banner'],
        ['id' => 6, 'image' => '/img/blog/1.webp', 'title' => 'The best way to change your summer wardrobe into autumn wardrobe', 'size' => 'small'],
        ['id' => 7, 'image' => '/img/blog/2.webp', 'title' => "Lenovo's smarter devices stoke professional passions", 'size' => 'small'],
        ['id' => 8, 'image' => '/img/blog/3.webp', 'title' => 'Take a 3D tour through a Microsoft datacenter', 'size' => 'small'],
    ];

    return Inertia::render('Blog/Index', [
        'posts' => $posts,
    ]);
})->name('blog');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::post('/contact', function () {
    request()->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email'],
        'subject' => ['required', 'string', 'max:255'],
        'message' => ['required', 'string', 'max:5000'],
    ]);

    return redirect()
        ->route('contact')
        ->with('success', 'Thank you! Your message has been sent.');
})->name('contact.submit');

Route::get('/cart', function () {
    return Inertia::render('Cart');
})->name('cart');

Route::get('/checkout', function () {
    return Inertia::render('Checkout');
})->name('checkout');

Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::patch('/orders/{order}', [DashboardController::class, 'updateStatus'])
        ->name('dashboard.orders.update-status');

    Route::get('/products', [ProductController::class, 'index'])->name('dashboard.products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('dashboard.products.store');
    Route::put('/products/{product:id}', [ProductController::class, 'update'])->name('dashboard.products.update');
    Route::delete('/products/{product:id}', [ProductController::class, 'destroy'])->name('dashboard.products.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('dashboard.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('dashboard.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('dashboard.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('dashboard.categories.destroy');

    Route::get('/brands', [BrandController::class, 'index'])->name('dashboard.brands.index');
    Route::post('/brands', [BrandController::class, 'store'])->name('dashboard.brands.store');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('dashboard.brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('dashboard.brands.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
