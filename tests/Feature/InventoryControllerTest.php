<?php

namespace Tests\Feature;

use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;

class InventoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the creation of a new inventory.
     *
     * @return void
     */
    public function test_create_inventory()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/inventories/create', [
            'capacity' => 100,
            'current_stock' => 50,
            'location' => 'Warehouse A',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Inventory created successfully',
            ]);

        $this->assertDatabaseHas('inventories', [
            'capacity' => 100,
            'current_stock' => 50,
            'location' => 'Warehouse A',
        ]);
    }

    /**
     * Test the creation of inventory with invalid data.
     *
     * @return void
     */
    public function test_create_inventory_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->postJson('/api/inventories/create', [
            'capacity' => 50,
            'current_stock' => 100, // Invalid: current_stock is greater than capacity
            'location' => 'Warehouse A',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => false,
                'message' => 'Current stock cannot be greater than capacity',
            ]);
    }

    /**
     * Test retrieving all inventories.
     *
     * @return void
     */
    public function test_get_all_inventories()
    {
        $user = User::factory()->create();
$this->actingAs($user);
        $inventory = Inventory::factory()->create();

        $response = $this->getJson('/api/inventories/index');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'All inventories',
            ])
            ->assertJsonFragment([
                'capacity' => $inventory->capacity,
                'current_stock' => $inventory->current_stock,
                'location' => $inventory->location,
            ]);
    }

    /**
     * Test updating an inventory.
     *
     * @return void
     */
    public function test_update_inventory()
    {
        $user = User::factory()->create();
$this->actingAs($user);
        $inventory = Inventory::factory()->create();

        $response = $this->putJson('/api/inventories/update/' . $inventory->id, [
            'capacity' => 200,
            'current_stock' => 100,
            'location' => 'Warehouse B',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Inventory updated successfully',
            ]);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'capacity' => 200,
            'current_stock' => 100,
            'location' => 'Warehouse B',
        ]);
    }

    /**
     * Test updating a non-existing inventory.
     *
     * @return void
     */
    public function test_update_non_existing_inventory()
    {
        $user = User::factory()->create();
$this->actingAs($user);
        $response = $this->putJson('/api/inventories/update/999999', [
            'capacity' => 200,
            'current_stock' => 100,
            'location' => 'Warehouse B',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'message' => 'Inventory not found',
            ]);
    }

    /**
     * Test deleting an inventory.
     *
     * @return void
     */
    public function test_delete_inventory()
    {
        $user = User::factory()->create();
$this->actingAs($user);
        $inventory = Inventory::factory()->create();

        $response = $this->deleteJson('/api/inventories/delete/' . $inventory->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Inventory deleted successfully',
            ]);

        $this->assertDatabaseMissing('inventories', [
            'id' => $inventory->id,
        ]);
    }

    /**
     * Test deleting a non-existing inventory.
     *
     * @return void
     */
    public function test_delete_non_existing_inventory()
    {
        $user = User::factory()->create();
$this->actingAs($user);
        $response = $this->deleteJson('/api/inventories/delete/999999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'message' => 'Inventory not found',
            ]);
    }
}
