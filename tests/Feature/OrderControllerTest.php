<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Customer $customer;

    // Set up the necessary data for tests
    protected function setUp(): void
    {
        parent::setUp();

        // Create users, customers, and other necessary data
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->customer = Customer::factory()->create();
    }

    #[Test]
    public function it_can_create_an_order()
    {
        $response = $this->actingAs($this->user)
                         ->postJson('/api/orders/create', [
                             'total_price' => 100.00,
                             'status' => 'processing',
                             'customer_id' => $this->customer->id,
                             'user_id' => $this->user->id,
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Order created successfully',
                 ]);

        $this->assertDatabaseHas('orders', [
            'total_price' => 100.00,
            'status' => 'processing',
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_can_get_all_orders()
    {
        $order = Order::factory()->create([
            'total_price' => 100.00,
            'status' => 'processing',
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
                         ->getJson('/api/orders/index');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'All orders',
                 ])
                 ->assertJsonFragment([
                     'total_price' => 100.00,
                     'status' => 'processing',
                     'customer_id' => $this->customer->id,
                     'user_id' => $this->user->id,
                 ]);
    }

    #[Test]
    public function it_can_update_an_order()
    {
        $order = Order::factory()->create([
            'total_price' => 100.00,
            'status' => 'processing',
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
                         ->putJson("/api/orders/update/{$order->id}", [
                             'total_price' => 150.00,
                             'status' => 'completed',
                             'customer_id' => $this->customer->id,
                             'user_id' => $this->user->id,
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Order updated successfully',
                 ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total_price' => 150.00,
            'status' => 'completed',
        ]);
    }

    #[Test]
    public function it_can_delete_an_order()
    {
        $order = Order::factory()->create([
            'total_price' => 100.00,
            'status' => 'processing',
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
                         ->deleteJson("/api/orders/delete/{$order->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Order deleted successfully',
                 ]);

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }
}
