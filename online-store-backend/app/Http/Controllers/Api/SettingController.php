<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SettingController extends Controller
{
    /**
     * جلب كل الإعدادات العامة (عام)
     */
    public function index(): AnonymousResourceCollection
    {
        $settings = Setting::all();

        return SettingResource::collection($settings);
    }

    /**
     * جلب كل الإعدادات العامة بتفاصيلها للأدمن
     */
    public function adminIndex(): AnonymousResourceCollection
    {
        return SettingResource::collection(Setting::all());
    }

    /**
     * تعديل الإعدادات (أدمن فقط)
     * يقبل JSON: { "whatsapp_number": "218...", "exchange_rate": "7.08" }
     */
    public function update(Request $request): JsonResponse
    {
        // جلب المفاتيح الموجودة مسبقاً في قاعدة البيانات والتي تم إنشاؤها عبر الـ Seeder
        $allowedKeys = Setting::pluck('key')->toArray();

        // إدخالات المستخدم
        $settings = $request->all();

        foreach ($settings as $key => $value) {
            // تحديث الإعداد فقط إذا كان موجوداً بالفعل في قاعدة البيانات (التي تم وضعها عبر Seeder)
            if (in_array($key, $allowedKeys)) {
                Setting::setValue($key, $value);
            }
        }

        return response()->json([
            'message' => 'تم تحديث الإعدادات بنجاح.',
            'data' => SettingResource::collection(Setting::all()),
        ]);
    }
}
