<?php

namespace App\Http\Controllers\Api;

use App\Filters\ProductFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * عرض كل المنتجات (مع فلاتر)
     */
    public function index(Request $request, ProductFilters $filters): AnonymousResourceCollection
    {
        $query = Product::with(['categories', 'images'])->filter($filters)->latest();

        /** @var \App\Models\User|null $user */
        $user = auth('sanctum')->user();

        // فلترة المنتجات النشطة فقط للزوار
        if (! $user?->hasManagementAccess()) {
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
    public function show(string $identifier): ProductResource
    {
        $product = Product::with(['categories', 'images'])
            ->where(function ($query) use ($identifier) {
                if (ctype_digit($identifier)) {
                    $query->whereKey((int) $identifier)
                        ->orWhere('slug', $identifier);

                    return;
                }

                $query->where('slug', $identifier);
            })
            ->firstOrFail();

        return new ProductResource($product);
    }

    /**
     * إنشاء منتج جديد (أدمن)
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

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
        if ($product->orderItems()->exists()) {
            return response()->json([
                'message' => 'لا يمكن حذف المنتج لأنه مرتبط بطلبات سابقة. يمكنك تعطيله بدلاً من ذلك.',
            ], 409);
        }

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $product->categories()->detach();

        try {
            $product->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => 'تعذر حذف المنتج حالياً بسبب وجود بيانات مرتبطة به.',
            ], 409);
        }

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
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json([
            'message' => 'تم حذف الصورة بنجاح.',
        ]);
    }
}
