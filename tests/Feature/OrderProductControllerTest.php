<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OrderProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed'); 
    }

    // Test adding a product to an order
    public function test_add_product_to_order()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson("/api/orders/{$order->id}/products/{$product->id}/create", [
            'quantity' => 10,
            'price' => 99.99,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product added to order successfully',
            ]);

        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'price' => 99.99,
        ]);
    }

    // Test getting all order products
    public function test_get_all_order_products()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $order->products()->attach($product, ['quantity' => 5, 'price' => 49.99]);

        $response = $this->getJson('/api/orders/products/index');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'All OrderProducts',
            ])
            ->assertJsonFragment([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 5,
                'price' => 49.99,
            ]);
    }

    // Test updating a product in an order
    public function test_update_product_in_order()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $order->products()->attach($product, ['quantity' => 5, 'price' => 49.99]);

        $response = $this->putJson("/api/orders/{$order->id}/products/{$product->id}/update", [
            'quantity' => 15,
            'price' => 89.99,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product updated in order successfully',
            ]);

        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 15,
            'price' => 89.99,
        ]);
    }

    // Test deleting a product from an order
    public function test_delete_product_from_order()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $order->products()->attach($product, ['quantity' => 5, 'price' => 49.99]);

        $response = $this->deleteJson("/api/orders/{$order->id}/products/{$product->id}/delete");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product deleted from order successfully',
            ]);

        $this->assertDatabaseMissing('order_product', [
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);
    }
}
