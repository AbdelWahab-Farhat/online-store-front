<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    protected $appends = ['label'];

    /**
     * إرجاع عنوان عربي لكل إعداد لسهولة عرضه في لوحة التحكم
     */
    public function getLabelAttribute(): string
    {
        return match ($this->key) {
            'whatsapp_number' => 'رقم الواتساب',
            'contact_phone' => 'رقم هاتف التواصل',
            'location' => 'الموقع',
            'contact_email' => 'إيميل التواصل',
            'instagram_url' => 'صفحة الانستغرام',
            'facebook_url' => 'صفحة الفيسبوك',
            'exchange_rate' => 'سعر الصرف (دولار/دينار)',
            'store_name' => 'اسم المتجر',
            default => $this->string_to_human_readable($this->key), // كإجراء احتياطي لأي مفتاح غير معروف
        };
    }

    private function string_to_human_readable(string $string): string
    {
        return ucfirst(str_replace('_', ' ', $string));
    }

    // تمت إزالة ALLOWED_KEYS لأنه سيتم الاعتماد على المفاتيح الموجودة في قاعدة البيانات (داينمك عبر الـ Seeder)

    // ---- Static Helpers ----

    /**
     * جلب قيمة إعداد حسب المفتاح
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * تعيين قيمة إعداد
     */
    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }

}
