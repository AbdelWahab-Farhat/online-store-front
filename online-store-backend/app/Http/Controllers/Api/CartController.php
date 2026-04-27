<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CartController extends Controller
{
    /**
     * عرض سلة المشتريات
     */
    public function index(Request $request): JsonResponse
    {
        $items = CartItem::with('product.images')
            ->where('user_id', $request->user()->id)
            ->get();

        $total = $items->sum(fn ($item) => $item->product->price * $item->quantity);

        return response()->json([
            'data' => CartItemResource::collection($items),
            'cart_total' => number_format($total, 2, '.', ''),
            'items_count' => $items->sum('quantity'),
        ]);
    }

    /**
     * إضافة منتج للسلة أو زيادة الكمية
     * POST { product_id, quantity? }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $quantity = $validated['quantity'] ?? 1;

        // التحقق من أن المنتج نشط ومتوفر
        if (! $product->is_active) {
            return response()->json([
                'message' => 'هذا المنتج غير متاح حالياً.',
            ], 422);
        }

        // إذا المنتج موجود بالسلة، نزيد الكمية
        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;

            // التحقق من توفر الكمية بالمخزون
            if ($newQuantity > $product->quantity) {
                return response()->json([
                    'message' => 'الكمية المطلوبة غير متوفرة. المتوفر: ' . $product->quantity,
                ], 422);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // التحقق من توفر الكمية بالمخزون
            if ($quantity > $product->quantity) {
                return response()->json([
                    'message' => 'الكمية المطلوبة غير متوفرة. المتوفر: ' . $product->quantity,
                ], 422);
            }

            $cartItem = CartItem::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        $cartItem->load('product.images');

        return response()->json([
            'message' => 'تمت الإضافة إلى السلة.',
            'data' => new CartItemResource($cartItem),
        ], 201);
    }

    /**
     * تعديل كمية منتج بالسلة
     * PUT { quantity }
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        // التأكد أن العنصر يخص المستخدم الحالي
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        // التحقق من توفر الكمية بالمخزون
        if ($validated['quantity'] > $cartItem->product->quantity) {
            return response()->json([
                'message' => 'الكمية المطلوبة غير متوفرة. المتوفر: ' . $cartItem->product->quantity,
            ], 422);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);
        $cartItem->load('product.images');

        return response()->json([
            'message' => 'تم تحديث الكمية.',
            'data' => new CartItemResource($cartItem),
        ]);
    }

    /**
     * حذف منتج من السلة
     */
    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        // التأكد أن العنصر يخص المستخدم الحالي
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'تم حذف المنتج من السلة.',
        ]);
    }

    /**
     * تفريغ السلة بالكامل
     */
    public function clear(Request $request): JsonResponse
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'تم تفريغ السلة.',
        ]);
    }
}
