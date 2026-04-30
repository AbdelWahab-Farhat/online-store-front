<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ManagementDeleteActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_delete_category_that_contains_products(): void
    {
        Sanctum::actingAs($this->makeAdminUser());

        $category = Category::create([
            'name' => 'الإلكترونيات',
            'slug' => 'electronics',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'هاتف ذكي',
            'slug' => 'smart-phone',
            'description' => 'منتج تجريبي',
            'price' => 2500,
            'quantity' => 5,
            'is_active' => true,
        ]);

        $product->categories()->attach($category->id);

        $response = $this->deleteJson("/api/admin/categories/{$category->id}");

        $response
            ->assertStatus(409)
            ->assertJson([
                'message' => 'لا يمكن حذف التصنيف لأنه يحتوي على منتجات مرتبطة به.',
            ]);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_delete_empty_category_and_its_image(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->makeAdminUser());

        $category = Category::create([
            'name' => 'العطور',
            'slug' => 'perfumes',
            'is_active' => true,
        ]);

        Storage::disk('public')->put('categories/perfumes.jpg', 'fake-image');
        $image = $category->image()->create([
            'path' => 'categories/perfumes.jpg',
            'sort_order' => 0,
        ]);

        $response = $this->deleteJson("/api/admin/categories/{$category->id}");

        $response->assertOk()->assertJson([
            'message' => 'تم حذف التصنيف بنجاح.',
        ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('categories/perfumes.jpg');
    }

    public function test_admin_can_delete_product_and_its_images_when_it_has_no_orders(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->makeAdminUser());

        $category = Category::create([
            'name' => 'الحقائب',
            'slug' => 'bags',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'حقيبة جلد',
            'slug' => 'leather-bag',
            'description' => 'منتج تجريبي',
            'price' => 420,
            'quantity' => 7,
            'is_active' => true,
        ]);

        $product->categories()->attach($category->id);

        Storage::disk('public')->put('products/leather-bag.jpg', 'fake-image');
        $image = $product->images()->create([
            'path' => 'products/leather-bag.jpg',
            'sort_order' => 0,
        ]);

        $response = $this->deleteJson("/api/admin/products/{$product->id}");

        $response->assertOk()->assertJson([
            'message' => 'تم حذف المنتج بنجاح.',
        ]);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        $this->assertDatabaseMissing('category_product', [
            'category_id' => $category->id,
            'product_id' => $product->id,
        ]);
        Storage::disk('public')->assertMissing('products/leather-bag.jpg');
    }

    public function test_admin_cannot_delete_product_that_is_referenced_by_orders(): void
    {
        Sanctum::actingAs($this->makeAdminUser());

        $product = Product::create([
            'name' => 'ساعة يد',
            'slug' => 'watch',
            'description' => 'منتج تجريبي',
            'price' => 180,
            'quantity' => 3,
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'status' => 'pending',
            'subtotal' => 180,
            'discount_amount' => 0,
            'delivery_cost' => 0,
            'total' => 180,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0912345678',
            'customer_email' => 'test@example.com',
            'city' => 'Tripoli',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $product->price,
            'total_price' => $product->price,
        ]);

        $response = $this->deleteJson("/api/admin/products/{$product->id}");

        $response
            ->assertStatus(409)
            ->assertJson([
                'message' => 'لا يمكن حذف المنتج لأنه مرتبط بطلبات سابقة. يمكنك تعطيله بدلاً من ذلك.',
            ]);

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    private function makeAdminUser(): User
    {
        return User::factory()->create([
            'role' => Role::Admin,
        ]);
    }
}
