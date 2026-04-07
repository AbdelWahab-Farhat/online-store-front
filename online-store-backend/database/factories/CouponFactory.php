<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
use App\Enums\DiscountType;

class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('COUPON-#####')),
            'discount_type' => fake()->randomElement([DiscountType::Percentage, DiscountType::Fixed]),
            'discount_value' => fake()->randomFloat(2, 5, 50),
            'min_order_amount' => fake()->optional(0.5)->randomFloat(2, 50, 200),
            'max_discount' => fake()->optional(0.5)->randomFloat(2, 10, 100),
            'usage_limit' => fake()->optional()->numberBetween(10, 100),
            'used_count' => 0,
            'starts_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'expires_at' => fake()->dateTimeBetween('now', '+2 months'),
            'is_active' => fake()->boolean(90), // 90% chance to be active
        ];
    }
}
