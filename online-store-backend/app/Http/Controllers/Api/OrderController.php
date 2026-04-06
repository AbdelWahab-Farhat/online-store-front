<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * عرض كل الطلبات (أدمن يشوف الكل، المستخدم يشوف طلباته)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Order::with(['items', 'coupon']);

        if (! $request->user()->hasManagementAccess()) {
            $query->where('user_id', $request->user()->id);
        }

        // فلتر حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15);

        return OrderResource::collection($orders);
    }

    /**
     * عرض تفاصيل طلب
     */
    public function show(Request $request, Order $order): OrderResource|JsonResponse
    {
        // التأكد إن المستخدم يملك الطلب أو أدمن
        if (! $request->user()->hasManagementAccess() && $order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $order->load(['items', 'coupon']);

        return new OrderResource($order);
    }

    /**
     * إنشاء طلب جديد
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $subtotal = 0;
            $orderItems = [];

            // حساب المنتجات والتأكد من توفرها
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if (! $product->is_active) {
                    return response()->json([
                        'message' => "المنتج '{$product->name}' غير متاح حالياً.",
                    ], 422);
                }

                if ($product->quantity < $item['quantity']) {
                    return response()->json([
                        'message' => "الكمية المطلوبة من '{$product->name}' غير متوفرة. المتوفر: {$product->quantity}",
                    ], 422);
                }

                $itemTotal = $product->price * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product' => $product,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $itemTotal,
                ];
            }

            // التحقق من الكوبون
            $discountAmount = 0;
            $couponId = null;

            if ($request->filled('coupon_code')) {
                $coupon = Coupon::where('code', $request->coupon_code)->first();

                if ($coupon && $coupon->isValid()) {
                    if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
                        return response()->json([
                            'message' => "الحد الأدنى للطلب لاستخدام الكوبون هو {$coupon->min_order_amount}.",
                        ], 422);
                    }

                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    $couponId = $coupon->id;

                    // زيادة عداد الاستخدام
                    $coupon->increment('used_count');
                }
            }

            $total = $subtotal - $discountAmount;

            // إنشاء الطلب
            $order = Order::create([
                'user_id' => $request->user()?->id,
                'coupon_id' => $couponId,
                'status' => OrderStatus::Pending,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'delivery_cost' => 0,
                'total' => $total,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'city' => $request->city,
                'notes' => $request->notes,
            ]);

            // إنشاء عناصر الطلب وتقليل المخزون
            foreach ($orderItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                // تخفيض الكمية من المخزون
                $item['product']->decrement('quantity', $item['quantity']);
            }

            $order->load(['items', 'coupon']);

            return response()->json([
                'message' => 'تم إنشاء الطلب بنجاح.',
                'data' => new OrderResource($order),
            ], 201);
        });
    }

    /**
     * تحديث حالة الطلب (أدمن)
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $status = OrderStatus::tryFrom($request->status);

        if (! $status) {
            return response()->json([
                'message' => 'حالة الطلب غير صحيحة.',
                'allowed' => array_column(OrderStatus::cases(), 'value'),
            ], 422);
        }

        $order->update(['status' => $status]);

        return response()->json([
            'message' => 'تم تحديث حالة الطلب بنجاح.',
            'data' => new OrderResource($order->load(['items', 'coupon'])),
        ]);
    }
}
