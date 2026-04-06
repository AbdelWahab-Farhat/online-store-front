<?php

namespace App\Http\Controllers\Api;

use App\Filters\CategoryFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * عرض كل التصنيفات
     */
    public function index(Request $request, CategoryFilters $filters): AnonymousResourceCollection
    {
        $query = Category::with('image')->withCount('products')->filter($filters);

        // فلترة التصنيفات النشطة فقط للزوار
        if (! $request->user()?->hasManagementAccess()) {
            $query->active();
        }

        $categories = $query->orderBy('name')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * عرض تصنيف واحد
     */
    public function show(Category $category): CategoryResource
    {
        $category->load('image')->loadCount('products');

        return new CategoryResource($category);
    }

    /**
     * إنشاء تصنيف جديد (أدمن)
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        // حذف الصورة من بيانات الإنشاء
        $categoryData = collect($data)->except(['image'])->toArray();
        $category = Category::create($categoryData);

        // رفع صورة التصنيف (واحدة فقط)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $category->image()->create(['path' => $path]);
        }

        $category->load('image');

        return response()->json([
            'message' => 'تم إنشاء التصنيف بنجاح.',
            'data' => new CategoryResource($category),
        ], 201);
    }

    /**
     * تعديل تصنيف (أدمن)
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $categoryData = collect($data)->except(['image'])->toArray();
        $category->update($categoryData);

        // استبدال الصورة (حذف القديمة + رفع الجديدة)
        if ($request->hasFile('image')) {
            $category->image?->delete();
            $path = $request->file('image')->store('categories', 'public');
            $category->image()->create(['path' => $path]);
        }

        $category->load('image');

        return response()->json([
            'message' => 'تم تعديل التصنيف بنجاح.',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * حذف تصنيف (أدمن)
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'message' => 'تم حذف التصنيف بنجاح.',
        ]);
    }
}
