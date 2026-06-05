<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class StorefrontCatalog
{
    private static ?array $data = null;

    public static function data(): array
    {
        if (self::$data === null) {
            self::$data = require database_path('data/storefront-catalog.php');
        }

        return self::$data;
    }

    public static function categories(): array
    {
        return self::data()['categories'];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function brands(): array
    {
        return self::data()['brands'];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function products(): array
    {
        return self::data()['products'];
    }

    /**
     * @return list<string>
     */
    public static function homeSectionSlugs(string $section): array
    {
        return self::data()['home_sections'][$section] ?? [];
    }

    /**
     * @return Collection<int, Product>
     */
    public static function homeSectionProducts(string $section): Collection
    {
        $slugs = self::homeSectionSlugs($section);

        if ($slugs === []) {
            return new Collection;
        }

        $products = Product::query()
            ->whereIn('slug', $slugs)
            ->get()
            ->keyBy('slug');

        return new Collection(
            collect($slugs)
                ->map(fn (string $slug): ?Product => $products->get($slug))
                ->filter()
                ->values()
                ->all(),
        );
    }

    public static function formatPrice(float $amount): string
    {
        return '$'.number_format($amount, 2);
    }

    /**
     * @param  list<string>|null  $galleryImages
     * @return list<string>
     */
    public static function resolveGalleryImages(?string $imageUrl, ?array $galleryImages): array
    {
        if ($imageUrl === null || $imageUrl === '') {
            return [];
        }

        $extras = array_values(array_filter($galleryImages ?? []));

        if ($extras === []) {
            return [$imageUrl];
        }

        return array_values(array_unique([$imageUrl, ...$extras]));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function productSeederRows(): array
    {
        return self::products();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function brandSeederRows(): array
    {
        return self::brands();
    }
}
