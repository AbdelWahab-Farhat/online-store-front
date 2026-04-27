<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        $titles = [
            'مجوهرات تخطف الأنظار',
            'تشكيلة أزياء الموسم',
            'عروض حصرية على الحقائب',
            'أحذية عصرية بأفضل الأسعار',
            'مستحضرات العناية بالبشرة',
            'تخفيضات نهاية الموسم',
        ];

        $descriptions = [
            'أضفي لمسة ذهبية لإطلالتك',
            'اكتشفي أحدث صيحات الموضة',
            'تسوقي الآن واحصلي على خصم مميز',
            'جودة عالية وأسعار منافسة',
            'منتجات طبيعية لبشرة مثالية',
            'خصومات تصل إلى 50%',
        ];

        return [
            'title' => fake()->randomElement($titles),
            'description' => fake()->randomElement($descriptions),
            'is_active' => fake()->boolean(70),
            'sort_order' => fake()->numberBetween(0, 5),
            'link' => fake()->optional(0.7)->url(),
            'link_text' => fake()->optional(0.7)->randomElement(['تسوقي الآن', 'اكتشفي المزيد', 'عرض العروض']),
        ];
    }

    /**
     * State: لوحة نشطة
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * State: لوحة غير نشطة
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
