<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء 4 لوحات إعلانية (من أصل 6 كحد أقصى)
        Announcement::factory()->count(4)->create();
    }
}
