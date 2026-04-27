<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // يمكنك إضافة أي إعدادات جديدة هنا، وسيتعرف عليها النظام وتصبح قابلة للتعديل تلقائياً
        $settings = [
            [
                'key' => 'whatsapp_number',
                'value' => '218912345678',
            ],
            [
                'key' => 'contact_phone',
                'value' => '218911111111',
            ],
            [
                'key' => 'location',
                'value' => 'طرابلس، ليبيا',
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@example.com',
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/store',
            ],
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/store',
            ],
        ];

        foreach ($settings as $setting) {
            // نستخدم firstOrCreate كي لا يتم مسح القيمة الحالية في حال عملنا Seeding مرة أخرى
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
