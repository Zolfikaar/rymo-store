<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Support\StorefrontCatalog;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        foreach (StorefrontCatalog::brandSeederRows() as $row) {
            Brand::query()->updateOrCreate(
                ['slug' => $row['slug']],
                ['name' => $row['name']],
            );
        }
    }
}
