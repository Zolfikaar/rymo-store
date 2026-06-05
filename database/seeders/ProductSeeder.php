<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\StorefrontCatalog;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'slug');
        $brands = Brand::query()->pluck('id', 'slug');

        foreach (StorefrontCatalog::productSeederRows() as $row) {
            $categoryId = $categories[$row['category_slug']] ?? null;
            $brandId = $brands[$row['brand_slug'] ?? ''] ?? null;

            Product::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'price' => $row['price'],
                    'category_id' => $categoryId,
                    'brand_id' => $brandId,
                    'color' => $row['color'],
                    'size' => $row['size'],
                    'rating' => $row['rating'],
                    'stock' => $row['stock'],
                    'sku' => $row['sku'],
                    'image_url' => $row['image_url'],
                    'gallery_images' => $row['gallery_images'],
                    'available_sizes' => $row['available_sizes'],
                ],
            );
        }
    }
}
