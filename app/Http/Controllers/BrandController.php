<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandDashboardResource;
use App\Http\Resources\CategoryDashboardResource;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard/Catalog', [
            'activeTab' => 'brands',
            'categories' => CategoryDashboardResource::collection(
                Category::query()
                    ->withCount('products')
                    ->orderBy('name')
                    ->get(),
            ),
            'brands' => BrandDashboardResource::collection(
                Brand::query()
                    ->withCount('products')
                    ->orderBy('name')
                    ->get(),
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Brand::query()->create([
            'name' => $validated['name'],
            'slug' => $this->resolveSlug($validated['name'], $validated['slug'] ?? null),
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate($this->rules($brand));

        $brand->update([
            'name' => $validated['name'],
            'slug' => $this->resolveSlug($validated['name'], $validated['slug'] ?? null),
        ]);

        return redirect()->back();
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            throw ValidationException::withMessages([
                'brand' => 'Cannot delete a brand that still has products assigned.',
            ]);
        }

        DB::transaction(function () use ($brand): void {
            $brand->delete();
        });

        return redirect()->back();
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?Brand $brand = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brands', 'slug')->ignore($brand?->id),
            ],
        ];
    }
}
