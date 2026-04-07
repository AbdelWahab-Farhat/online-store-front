<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'مكياج الوجه', 'slug' => 'face-makeup'],
            ['name' => 'مكياج العيون', 'slug' => 'eye-makeup'],
            ['name' => 'مجوهرات', 'slug' => 'jewelry'],
            ['name' => 'حقائب', 'slug' => 'bags'],
            ['name' => 'عطور', 'slug' => 'perfumes'],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['is_active' => true]));
        }
    }
}
