<?php

namespace App\Http\Controllers\Api;

use App\Filters\ProductFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * عرض كل المنتجات (مع فلاتر)
     */
    public function index(Request $request, ProductFilters $filters): AnonymousResourceCollection
    {
        $query = Product::with(['categories', 'images'])->filter($filters);

        // فلترة المنتجات النشطة فقط للزوار
        if (! $request->user()?->hasManagementAccess()) {
            $query->active();
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 50);
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * عرض منتج واحد (بالـ slug)
     */
    public function show(string $slug): ProductResource
    {
        $product = Product::with(['categories', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();

        return new ProductResource($product);
    }

    /**
     * إنشاء منتج جديد (أدمن)
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        // حذف الحقول اللي ما تخص جدول المنتج
        $productData = collect($data)->except(['category_ids', 'images'])->toArray();

        $product = Product::create($productData);

        // ربط التصنيفات
        $product->categories()->attach($data['category_ids']);

        // رفع الصور
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        $product->load(['categories', 'images']);

        return response()->json([
            'message' => 'تم إنشاء المنتج بنجاح.',
            'data' => new ProductResource($product),
        ], 201);
    }

    /**
     * تعديل منتج (أدمن)
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $productData = collect($data)->except(['category_ids', 'images'])->toArray();
        $product->update($productData);

        // تحديث التصنيفات
        if (isset($data['category_ids'])) {
            $product->categories()->sync($data['category_ids']);
        }

        // إضافة صور جديدة
        if ($request->hasFile('images')) {
            $maxSort = $product->images()->max('sort_order') ?? -1;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'sort_order' => $maxSort + $index + 1,
                ]);
            }
        }

        $product->load(['categories', 'images']);

        return response()->json([
            'message' => 'تم تعديل المنتج بنجاح.',
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * حذف منتج (أدمن)
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'تم حذف المنتج بنجاح.',
        ]);
    }

    /**
     * حذف صورة من منتج (أدمن)
     */
    public function deleteImage(Product $product, int $imageId): JsonResponse
    {
        $image = $product->images()->findOrFail($imageId);
        $image->delete();

        return response()->json([
            'message' => 'تم حذف الصورة بنجاح.',
        ]);
    }
}
