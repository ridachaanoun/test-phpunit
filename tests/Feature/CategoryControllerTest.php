<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations
        Artisan::call('migrate');
    }

    #[Test]
    public function user_can_create_category()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/categories/create', [
            'name' => 'Test Category',
            'description' => 'This is a test category description',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Category created successfully',
                     'category' => [
                         'name' => 'Test Category',
                         'description' => 'This is a test category description',
                     ],
                 ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'description' => 'This is a test category description',
        ]);
    }

    #[Test]
    public function user_can_get_all_categories()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a category
        Category::create([
            'name' => 'Existing Category',
            'description' => 'Description for existing category',
        ]);

        $response = $this->getJson('/api/categories/index');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'All categories',
                     'categories' => [
                         [
                             'name' => 'Existing Category',
                             'description' => 'Description for existing category',
                         ],
                     ],
                 ]);
    }

    #[Test]
    public function user_can_update_category()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a category to update
        $category = Category::create([
            'name' => 'Old Category',
            'description' => 'Old description',
        ]);

        $response = $this->putJson('/api/categories/update/' . $category->id, [
            'name' => 'Updated Category',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Category updated successfully',
                     'category' => [
                         'name' => 'Updated Category',
                         'description' => 'Updated description',
                     ],
                 ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Updated Category',
            'description' => 'Updated description',
        ]);
    }

    #[Test]
    public function user_can_delete_category()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a category to delete
        $category = Category::create([
            'name' => 'Category to be deleted',
            'description' => 'This category will be deleted',
        ]);

        $response = $this->deleteJson('/api/categories/delete/' . $category->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Category deleted successfully',
                 ]);

        $this->assertDatabaseMissing('categories', [
            'name' => 'Category to be deleted',
            'description' => 'This category will be deleted',
        ]);
    }
}
