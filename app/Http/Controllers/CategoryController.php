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

class CategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard/Catalog', [
            'activeTab' => 'categories',
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

        Category::query()->create([
            'name' => $validated['name'],
            'slug' => $this->resolveSlug($validated['name'], $validated['slug'] ?? null),
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate($this->rules($category));

        $category->update([
            'name' => $validated['name'],
            'slug' => $this->resolveSlug($validated['name'], $validated['slug'] ?? null),
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? $category->is_active,
        ]);

        return redirect()->back();
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Cannot delete a category that still has products assigned.',
            ]);
        }

        DB::transaction(function () use ($category): void {
            $category->delete();
        });

        return redirect()->back();
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?Category $category = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($category?->id),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
