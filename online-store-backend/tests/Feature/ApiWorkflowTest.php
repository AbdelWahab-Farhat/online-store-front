<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_workflow()
    {
        Storage::fake('public');

        $this->seed(\Database\Seeders\AdminSeeder::class);

        // 1. Login as admin
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'admin@admin.com',
            'password' => 'password123',
        ]);
        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');
        $this->assertNotNull($token);

        // 2. Create Category
        $categoryResponse = $this->withToken($token)->postJson('/api/admin/categories', [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);
        $categoryResponse->assertStatus(201);
        $categoryId = $categoryResponse->json('data.id');

        // 3. Create Product
        $productResponse = $this->withToken($token)->postJson('/api/admin/products', [
            'name' => 'Smartphone',
            'slug' => 'smartphone',
            'description' => 'A nice smartphone',
            'price' => 1000,
            'quantity' => 10,
            'is_active' => true,
            'category_ids' => [$categoryId],
        ]);
        $productResponse->assertStatus(201);
        $productId = $productResponse->json('data.id');

        // 4. Test fetch product as visitor
        $fetchProductResponse = $this->getJson('/api/products/smartphone');
        $fetchProductResponse->assertStatus(200);

        // 5. Create normal user
        $user = User::factory()->create();
        $userToken = $user->createToken('auth_token')->plainTextToken;

        // 6. Create Order
        $orderResponse = $this->withToken($userToken)->postJson('/api/orders', [
            'customer_name' => 'John Doe',
            'customer_phone' => '123456789',
            'customer_email' => 'john@test.com',
            'city' => 'Dubai',
            'items' => [
                [
                    'product_id' => $productId,
                    'quantity' => 2
                ]
            ]
        ]);
        $orderResponse->assertStatus(201);
        $this->assertEquals(2000, $orderResponse->json('data.subtotal'));

        // 7. Verify stock diminished
        $this->assertDatabaseHas('products', [
            'id' => $productId,
            'quantity' => 8,
        ]);
    }
}
