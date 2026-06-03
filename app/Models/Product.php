<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'category_id',
        'color',
        'size',
        'rating',
        'brand',
        'stock',
        'sku',
        'image_url',
        'gallery_images',
        'available_sizes',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'rating' => 'integer',
            'stock' => 'integer',
            'gallery_images' => 'array',
            'available_sizes' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
