<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Support\StorefrontCatalog;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'slug');

        foreach (StorefrontCatalog::productSeederRows() as $row) {
            $categoryId = $categories[$row['category_slug']] ?? null;

            Product::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'price' => $row['price'],
                    'category_id' => $categoryId,
                    'color' => $row['color'],
                    'size' => $row['size'],
                    'rating' => $row['rating'],
                    'brand' => $row['brand'],
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
