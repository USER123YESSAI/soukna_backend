<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 20), 1), 100);
        $q = trim((string) $request->string('q', ''));

        $query = Category::query()
            ->withCount([
                'products as products_count' => fn ($qq) => $qq->where('status', 'published'),
            ])
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $slugBase = $data['slug'] ?? Str::slug($data['name']);
        if ($slugBase === '') {
            $slugBase = 'category';
        }

        $slug = $this->generateUniqueSlug($slugBase);

        $category = Category::query()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
        ]);

        $category->loadCount([
            'products as products_count' => fn ($q) => $q->where('status', 'published'),
        ]);

        return response()->json([
            'category' => $category,
            'message' => __('Catégorie créée.'),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::query()->findOrFail($id);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        if (array_key_exists('name', $data) && $category->name !== $data['name']) {
            $category->name = $data['name'];
        }

        if (array_key_exists('slug', $data)) {
            $slugBase = $data['slug'];
            if ($slugBase === null || trim($slugBase) === '') {
                $slugBase = Str::slug($category->name);
            }
            if ($slugBase === '') {
                $slugBase = 'category';
            }
            $category->slug = $this->generateUniqueSlug($slugBase, $category->id);
        }

        if (array_key_exists('description', $data)) {
            $category->description = $data['description'];
        }
        if (array_key_exists('icon', $data)) {
            $category->icon = $data['icon'];
        }

        try {
            $category->save();
        } catch (QueryException $e) {
            // En particulier si slug unique clash
            throw $e;
        }

        $category->loadCount([
            'products as products_count' => fn ($q) => $q->where('status', 'published'),
        ]);

        return response()->json([
            'category' => $category->fresh(),
            'message' => __('Catégorie mise à jour.'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = Category::query()->findOrFail($id);

        try {
            $category->delete();
        } catch (QueryException $e) {
            abort(422, __('Impossible de supprimer cette catégorie. Elle est peut-être utilisée par des produits.'));
        }

        return response()->json(['message' => __('Catégorie supprimée.')]);
    }

    private function generateUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = Str::lower(trim($base));
        $base = preg_replace('/[^a-z0-9-]/', '', $base);
        $base = trim($base, '-');
        if ($base === '') {
            $base = 'category';
        }

        $slug = $base;
        $i = 2;

        while (
            Category::query()
                ->where('slug', $slug)
                ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}

