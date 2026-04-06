<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (بدون مصادقة)
|--------------------------------------------------------------------------
*/

// المصادقة
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// التصنيفات (عرض فقط)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// المنتجات (عرض فقط)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

// التحقق من كوبون
Route::post('/coupons/validate', [CouponController::class, 'validate']);

// إنشاء طلب (ضيف أو مسجل)
Route::post('/orders', [OrderController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes (تحتاج مصادقة)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // المصادقة
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);

    // طلبات المستخدم
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    /*
    |----------------------------------------------------------------------
    | Admin Routes (أدمن فقط)
    |----------------------------------------------------------------------
    */
    Route::middleware('is_admin')->prefix('admin')->group(function () {
        // التصنيفات
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // المنتجات
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        Route::delete('/products/{product}/images/{imageId}', [ProductController::class, 'deleteImage']);

        // الكوبونات
        Route::apiResource('/coupons', CouponController::class)->except(['validate']);

        // الطلبات
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    });
});
