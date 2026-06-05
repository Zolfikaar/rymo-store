<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandDashboardResource;
use App\Http\Resources\CategoryDashboardResource;
use App\Http\Resources\ProductDashboardResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\ProductImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /** @var list<string> */
    public const SIZE_OPTIONS = [
        'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL',
        '7', '8', '9', '10', '11', '12',
    ];

    public function index(): Response
    {
        $products = Product::query()
            ->with(['category', 'brand'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Dashboard/Products', [
            'products' => ProductDashboardResource::collection($products),
            'categories' => CategoryDashboardResource::collection(
                Category::query()->orderBy('name')->get(),
            ),
            'brands' => BrandDashboardResource::collection(
                Brand::query()->orderBy('name')->get(),
            ),
            'sizeOptions' => self::SIZE_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Product::query()->create($this->mapValidatedAttributes($request, $validated));

        return redirect()->back();
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate($this->rules($product));

        $attributes = $this->mapValidatedAttributes($request, $validated, $product);

        if (
            $request->hasFile('image')
            && $product->image_url !== null
            && $product->image_url !== $attributes['image_url']
        ) {
            ProductImageStorage::deleteIfStored($product->image_url);
        }

        $removedGalleryImages = collect($product->gallery_images ?? [])
            ->diff($attributes['gallery_images'] ?? [])
            ->filter(fn (mixed $url): bool => is_string($url));

        foreach ($removedGalleryImages as $removedGalleryImage) {
            ProductImageStorage::deleteIfStored($removedGalleryImage);
        }

        $product->update($attributes);

        return redirect()->back();
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            throw ValidationException::withMessages([
                'product' => 'Cannot delete a product that appears on existing orders.',
            ]);
        }

        DB::transaction(function () use ($product): void {
            ProductImageStorage::deleteIfStored($product->image_url);

            foreach ($product->gallery_images ?? [] as $galleryImage) {
                ProductImageStorage::deleteIfStored(is_string($galleryImage) ? $galleryImage : null);
            }

            $product->delete();
        });

        return redirect()->back();
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?Product $product = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product?->id),
            ],
            'description' => ['nullable', 'string', 'max:10000'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],
            'color' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'brand_id' => ['nullable', 'integer', Rule::exists('brands', 'id')],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'remove_image' => ['sometimes', 'boolean'],
            'existing_gallery_images' => ['nullable', 'array'],
            'existing_gallery_images.*' => ['string', 'max:2048'],
            'gallery_uploads' => ['nullable', 'array'],
            'gallery_uploads.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'available_sizes' => ['nullable', 'array'],
            'available_sizes.*' => ['string', Rule::in(self::SIZE_OPTIONS)],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function mapValidatedAttributes(Request $request, array $validated, ?Product $product = null): array
    {
        $availableSizes = collect($validated['available_sizes'] ?? [])
            ->filter(fn (mixed $size): bool => is_string($size) && $size !== '')
            ->unique()
            ->values()
            ->all();

        $galleryImages = $this->resolveGalleryImages($request);

        return [
            'name' => $validated['name'],
            'slug' => $this->resolveSlug(
                $validated['name'],
                $validated['slug'] ?? $product?->slug,
            ),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'sku' => $validated['sku'],
            'color' => $validated['color'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'image_url' => $this->resolveMainImageUrl($request, $product),
            'gallery_images' => $galleryImages,
            'available_sizes' => $availableSizes === [] ? null : $availableSizes,
        ];
    }

    private function resolveMainImageUrl(Request $request, ?Product $product = null): ?string
    {
        if ($request->boolean('remove_image')) {
            if ($product?->image_url) {
                ProductImageStorage::deleteIfStored($product->image_url);
            }

            return null;
        }

        if ($request->hasFile('image')) {
            if ($product?->image_url) {
                ProductImageStorage::deleteIfStored($product->image_url);
            }

            return ProductImageStorage::store($request->file('image'));
        }

        return $product?->image_url;
    }

    /**
     * @return list<string>|null
     */
    private function resolveGalleryImages(Request $request): ?array
    {
        $images = collect($request->input('existing_gallery_images', []))
            ->filter(fn (mixed $url): bool => is_string($url) && trim($url) !== '')
            ->map(fn (string $url): string => trim($url));

        /** @var list<UploadedFile|null> $uploads */
        $uploads = $request->file('gallery_uploads', []);

        foreach (ProductImageStorage::storeMany($uploads) as $storedPath) {
            $images->push($storedPath);
        }

        $gallery = $images->values()->all();

        return $gallery === [] ? null : $gallery;
    }
}
