<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations
        Artisan::call('migrate');
    }

    #[Test]
    public function user_can_create_customer()
    {
        // Create a user and act as that user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Send POST request to create a customer
        $response = $this->postJson('/api/customers/create', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ]);

        // Assert the response and database state
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Customer created successfully',
                     'customer' => [
                         'name' => 'John Doe',
                         'email' => 'john.doe@example.com',
                         'phone' => '1234567890',
                         'address' => '123 Main St',
                     ],
                 ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ]);
    }

    #[Test]
    public function user_can_get_all_customers()
    {
        // Create a user and act as that user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a customer to retrieve
        Customer::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '0987654321',
            'address' => '456 Elm St',
        ]);

        // Send GET request to retrieve all customers
        $response = $this->getJson('/api/customers/index');

        // Assert the response and database state
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'All customers',
                 ])
                 ->assertJsonFragment([
                     'name' => 'Jane Doe',
                     'email' => 'jane.doe@example.com',
                     'phone' => '0987654321',
                     'address' => '456 Elm St',
                 ]);
    }

    #[Test]
    public function user_can_update_customer()
    {
        // Create a user and act as that user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a customer to update
        $customer = Customer::create([
            'name' => 'Old Name',
            'email' => 'old.email@example.com',
            'phone' => '1234567890',
            'address' => 'Old Address',
        ]);

        // Send PUT request to update the customer
        $response = $this->putJson('/api/customers/update/' . $customer->id, [
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'phone' => '0987654321',
            'address' => 'Updated Address',
        ]);

        // Assert the response and database state
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Customer updated successfully',
                     'customer' => [
                         'name' => 'Updated Name',
                         'email' => 'updated.email@example.com',
                         'phone' => '0987654321',
                         'address' => 'Updated Address',
                     ],
                 ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'phone' => '0987654321',
            'address' => 'Updated Address',
        ]);
    }

    #[Test]
    public function user_can_delete_customer()
    {
        // Create a user and act as that user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a customer to delete
        $customer = Customer::create([
            'name' => 'Customer to Delete',
            'email' => 'delete.me@example.com',
            'phone' => '1234567890',
            'address' => 'To Be Deleted',
        ]);

        // Send DELETE request to delete the customer
        $response = $this->deleteJson('/api/customers/delete/' . $customer->id);

        // Assert the response and database state
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Customer deleted successfully',
                 ]);

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
            'name' => 'Customer to Delete',
            'email' => 'delete.me@example.com',
            'phone' => '1234567890',
            'address' => 'To Be Deleted',
        ]);
    }
}
