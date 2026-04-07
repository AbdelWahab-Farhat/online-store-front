<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 500),
            'compare_price' => fake()->optional(0.5)->randomFloat(2, 501, 1000),
            'quantity' => fake()->numberBetween(0, 100),
            'sku' => fake()->unique()->bothify('SKU-####-????'),
            'is_active' => true,
        ];
    }
}
