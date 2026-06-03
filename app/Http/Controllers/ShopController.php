<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCardResource;
use App\Http\Resources\ProductDetailResource;
use App\Models\Product;
use App\Support\StorefrontCatalog;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(): Response
    {
        $products = Product::query()
            ->orderBy('id')
            ->paginate(8)
            ->withQueryString();

        return Inertia::render('Shop/Index', [
            'products' => ProductCardResource::collection($products),
        ]);
    }

    public function show(Product $product): Response
    {
        $product->loadMissing('category');

        $relatedProducts = Product::query()
            ->where('slug', '!=', $product->slug)
            ->when(
                $product->category_id !== null,
                fn ($query) => $query->where('category_id', $product->category_id),
            )
            ->orderBy('id')
            ->limit(4)
            ->get();

        if ($relatedProducts->count() < 4) {
            $relatedProducts = $relatedProducts->concat(
                Product::query()
                    ->where('slug', '!=', $product->slug)
                    ->whereNotIn('id', $relatedProducts->pluck('id'))
                    ->orderBy('id')
                    ->limit(4 - $relatedProducts->count())
                    ->get(),
            );
        }

        return Inertia::render('Shop/Show', [
            'product' => ProductDetailResource::make($product)->resolve(),
            'relatedProducts' => ProductCardResource::collection($relatedProducts)->resolve(),
        ]);
    }

    public function home(): Response
    {
        return Inertia::render('Home', [
            'featuredProducts' => ProductCardResource::collection(
                StorefrontCatalog::homeSectionProducts('featured'),
            )->resolve(),
            'clothesProducts' => ProductCardResource::collection(
                StorefrontCatalog::homeSectionProducts('clothes'),
            )->resolve(),
            'watchesProducts' => ProductCardResource::collection(
                StorefrontCatalog::homeSectionProducts('watches'),
            )->resolve(),
            'shoesProducts' => ProductCardResource::collection(
                StorefrontCatalog::homeSectionProducts('shoes'),
            )->resolve(),
        ]);
    }
}
