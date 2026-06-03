<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Support\StorefrontCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductDetailResource extends JsonResource
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
            'category' => $this->categoryLabel(),
            'description' => $this->description ?? '',
            'images' => StorefrontCatalog::resolveGalleryImages(
                $this->image_url,
                $this->gallery_images,
            ),
            'sizes' => $this->available_sizes ?? [],
        ];
    }

    private function categoryLabel(): string
    {
        $categoryName = $this->relationLoaded('category')
            ? $this->category?->name
            : null;

        return $categoryName !== null
            ? "Home / {$categoryName}"
            : 'Home / Shop';
    }
}
