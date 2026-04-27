<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SettingController;
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

// اللوحات الإعلانية (عرض فقط)
Route::get('/announcements', [AnnouncementController::class, 'index']);
Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show']);

// الإعدادات العامة (عرض فقط)
Route::get('/settings', [SettingController::class, 'index']);

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

    // سلة المشتريات
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{cartItem}', [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

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

        // اللوحات الإعلانية
        Route::post('/announcements', [AnnouncementController::class, 'store']);
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update']);
        Route::patch('/announcements/{announcement}/toggle-active', [AnnouncementController::class, 'toggleActive']);
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy']);

        // الكوبونات
        Route::apiResource('/coupons', CouponController::class)->except(['validate']);

        // الطلبات
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);

        // الإعدادات العامة
        Route::get('/settings', [SettingController::class, 'adminIndex']); // Get All
        Route::put('/settings', [SettingController::class, 'update']);
    });
});
