<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;
    protected $adminUser;
    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and create an admin user
        $role = Role::create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $role->id,
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function it_can_create_a_permission()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum') // Or 'api' if using token-based auth
        ->postJson('/api/permissions/create', [
            'label' => 'Manage Users',
            'description' => 'Allows managing users'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Permission created successfully',
                 ]);
    }
    /** @test */
    public function it_can_get_all_permissions()
    {
        $permission = Permission::factory()->create([
            'label' => 'Edit Posts',
            'description' => 'Permission to edit posts',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/permissions/index');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Permissions fetched successfully',
                'permissions' => [
                    [
                        'label' => 'Edit Posts',
                        'description' => 'Permission to edit posts',
                    ]
                ],
            ]);
    }

    /** @test */
    public function it_can_update_a_permission()
    {
        $permission = Permission::factory()->create([
            'label' => 'Delete Posts',
            'description' => 'Permission to delete posts',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/permissions/update/{$permission->id}", [
                'label' => 'Delete Articles',
                'description' => 'Permission to delete articles',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Permission updated successfully',
            ]);

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'label' => 'Delete Articles',
            'description' => 'Permission to delete articles',
        ]);
    }

    /** @test */
    public function it_can_delete_a_permission()
    {
        $permission = Permission::factory()->create([
            'label' => 'Manage Users',
            'description' => 'Permission to manage users',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/permissions/delete/{$permission->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Permission deleted successfully',
            ]);

        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id,
        ]);
    }
}
