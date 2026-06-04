<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('brand')->nullable();
            $table->integer('stock')->default(0);
            $table->string('sku')->unique(); // Stock Keeping Unit, later will represent a unique identifier for each product variant (e.g., different sizes or colors) from product_variants table
            $table->string('image_url')->nullable();
            $table->json('gallery_images')->nullable();
            $table->json('available_sizes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
