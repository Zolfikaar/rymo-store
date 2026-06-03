<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Support\StorefrontCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductCardResource extends JsonResource
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
            'price' => StorefrontCatalog::formatPrice((float) $this->price),
            'image' => $this->image_url,
            'rating' => $this->rating,
        ];
    }
}
