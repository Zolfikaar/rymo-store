<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Support\StorefrontCatalog;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (StorefrontCatalog::categories() as $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
