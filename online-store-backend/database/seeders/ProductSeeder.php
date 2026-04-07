<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::factory(50)->create();

        foreach ($products as $product) {
            $product->categories()->attach(Category::inRandomOrder()->take(rand(1, 3))->pluck('id'));

            $product->images()->create([
                'path' => 'products/sample_product.png',
                'sort_order' => 1,
            ]);
        }
    }
}
