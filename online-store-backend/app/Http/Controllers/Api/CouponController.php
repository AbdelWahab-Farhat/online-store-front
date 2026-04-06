<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CouponController extends Controller
{
    /**
     * عرض كل الكوبونات (أدمن)
     */
    public function index(): AnonymousResourceCollection
    {
        $coupons = Coupon::latest()->get();

        return CouponResource::collection($coupons);
    }

    /**
     * إنشاء كوبون جديد (أدمن)
     */
    public function store(StoreCouponRequest $request): JsonResponse
    {
        $coupon = Coupon::create($request->validated());

        return response()->json([
            'message' => 'تم إنشاء الكوبون بنجاح.',
            'data' => new CouponResource($coupon),
        ], 201);
    }

    /**
     * عرض كوبون (أدمن)
     */
    public function show(Coupon $coupon): CouponResource
    {
        return new CouponResource($coupon);
    }

    /**
     * تعديل كوبون (أدمن)
     */
    public function update(Request $request, Coupon $coupon): JsonResponse
    {
        $coupon->update($request->only([
            'code', 'discount_type', 'discount_value',
            'min_order_amount', 'max_discount',
            'usage_limit', 'starts_at', 'expires_at', 'is_active',
        ]));

        return response()->json([
            'message' => 'تم تعديل الكوبون بنجاح.',
            'data' => new CouponResource($coupon),
        ]);
    }

    /**
     * حذف كوبون (أدمن)
     */
    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();

        return response()->json([
            'message' => 'تم حذف الكوبون بنجاح.',
        ]);
    }

    /**
     * التحقق من صلاحية كوبون (عام)
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'nullable|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (! $coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'كود الكوبون غير موجود.',
            ], 404);
        }

        if (! $coupon->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'الكوبون غير صالح أو منتهي الصلاحية.',
            ], 422);
        }

        if ($coupon->min_order_amount && $request->subtotal < $coupon->min_order_amount) {
            return response()->json([
                'valid' => false,
                'message' => "الحد الأدنى للطلب هو {$coupon->min_order_amount}.",
            ], 422);
        }

        $discount = $request->subtotal
            ? $coupon->calculateDiscount((float) $request->subtotal)
            : null;

        return response()->json([
            'valid' => true,
            'message' => 'الكوبون صالح.',
            'coupon' => new CouponResource($coupon),
            'discount_amount' => $discount,
        ]);
    }
}
