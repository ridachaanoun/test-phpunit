<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class InventoryProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed'); // Ensure database is seeded
    }

    // Test adding a product to inventory
    public function test_add_product_to_inventory()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
    
        $inventory = Inventory::factory()->create();
        $product = Product::factory()->create();
    
        $response = $this->postJson("/api/inventories/{$inventory->id}/products/{$product->id}/create", [
            'quantity' => 10,
        ]);
    
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product added to inventory successfully',
            ]);
    
        $this->assertDatabaseHas('inventory_product', [
            'inventory_id' => $inventory->id,
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
    }

    // Test getting all inventory products
    public function test_get_all_inventory_products()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $inventory = Inventory::factory()->create();
        $product = Product::factory()->create();

        $inventory->products()->attach($product, ['quantity' => 5]);

        $response = $this->getJson('/api/inventories/products/index');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'All InventoryProducts',
            ])
            ->assertJsonFragment([
                'inventory_id' => $inventory->id,
                'product_id' => $product->id,
                'quantity' => 5,
            ]);
    }

    // Test updating a product in inventory
    public function test_update_product_in_inventory()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $inventory = Inventory::factory()->create();
        $product = Product::factory()->create();

        $inventory->products()->attach($product, ['quantity' => 5]);

        $response = $this->putJson("/api/inventories/{$inventory->id}/products/{$product->id}/update", [
            'quantity' => 15,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product updated in inventory successfully',
            ]);

        $this->assertDatabaseHas('inventory_product', [
            'inventory_id' => $inventory->id,
            'product_id' => $product->id,
            'quantity' => 15,
        ]);
    }

    // Test deleting a product from inventory
    public function test_delete_product_from_inventory()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $inventory = Inventory::factory()->create();
        $product = Product::factory()->create();

        $inventory->products()->attach($product, ['quantity' => 5]);

        $response = $this->deleteJson("/api/inventories/{$inventory->id}/products/{$product->id}/delete");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product deleted from inventory successfully',
            ]);

        $this->assertDatabaseMissing('inventory_product', [
            'inventory_id' => $inventory->id,
            'product_id' => $product->id,
        ]);
    }
}
