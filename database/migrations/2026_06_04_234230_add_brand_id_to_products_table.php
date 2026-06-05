<?php

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable()->after('category_id');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
        });

        $brandNames = Product::query()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->pluck('brand');

        foreach ($brandNames as $brandName) {
            Brand::query()->firstOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName],
            );
        }

        $brandsBySlug = Brand::query()->pluck('id', 'slug');

        Product::query()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->each(function (Product $product) use ($brandsBySlug): void {
                $brandId = $brandsBySlug[Str::slug($product->brand)] ?? null;

                if ($brandId !== null) {
                    $product->update(['brand_id' => $brandId]);
                }
            });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('rating');
        });

        Product::query()
            ->with('brand')
            ->each(function (Product $product): void {
                $product->update(['brand' => $product->brand?->name]);
            });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });
    }
};
