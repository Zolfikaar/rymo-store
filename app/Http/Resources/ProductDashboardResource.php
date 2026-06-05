<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductDashboardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'price' => number_format((float) $this->price, 2, '.', ''),
            'stock' => $this->stock,
            'sku' => $this->sku,
            'color' => $this->color,
            'rating' => $this->rating,
            'image_url' => $this->image_url,
            'gallery_images' => $this->gallery_images ?? [],
            'available_sizes' => $this->available_sizes ?? [],
            'category_id' => $this->category_id,
            'category_name' => $this->relationLoaded('category') ? $this->category?->name : null,
            'brand_id' => $this->brand_id,
            'brand_name' => $this->relationLoaded('brand') ? $this->brand?->name : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
