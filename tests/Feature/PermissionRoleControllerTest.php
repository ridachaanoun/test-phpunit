<?php

// tests/Feature/PermissionRoleControllerTest.php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;

class PermissionRoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations
        Artisan::call('migrate');
        // Seed roles and permissions
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    #[Test]
    public function user_can_add_permission_to_role()
    {
        $user = $this->createAdminUser();
        $this->actingAs($user);

        // Create roles and permissions
        $role = Role::create(['name' => 'Manager', 'description' => 'Manager role']);
        $permission = Permission::create([
            'label' => 'edit',
            'description' => 'Permission to edit resources'  // Ensure this field is provided
        ]);

        $response = $this->postJson('/api/roles/' . $role->id . '/permissions/' . $permission->id . '/create');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Permission added to role successfully',
                 ]);

        $this->assertDatabaseHas('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);
    }

    #[Test]
    public function user_can_get_all_permission_roles()
    {
        $user = $this->createAdminUser();
        $this->actingAs($user);

        // Create roles and permissions
        $role = Role::create(['name' => 'Manager', 'description' => 'Manager role']);
        $permission = Permission::create([
            'label' => 'edit',
            'description' => 'Permission to edit resources'  // Ensure this field is provided
        ]);
        $role->permissions()->attach($permission);

        $response = $this->getJson('/api/roles/permissions/index');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'All Permissions for a role',
                     'permissions' => [
                         [
                             'role_id' => $role->id,
                             'permission_id' => $permission->id,
                         ],
                     ],
                 ]);
    }

    #[Test]
    public function user_can_delete_permission_from_role()
    {
        $user = $this->createAdminUser();
        $this->actingAs($user);

        // Create roles and permissions
        $role = Role::create(['name' => 'Manager', 'description' => 'Manager role']);
        $permission = Permission::create([
            'label' => 'edit',
            'description' => 'Permission to edit resources'  // Ensure this field is provided
        ]);
        $role->permissions()->attach($permission);

        $response = $this->deleteJson('/api/roles/' . $role->id . '/permissions/' . $permission->id . '/delete');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Permission deleted from role successfully',
                 ]);

        $this->assertDatabaseMissing('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);
    }

    // Helper method to create an admin user
    protected function createAdminUser()
    {
        $user = User::factory()->create();
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'description' => 'Administrator role with full permissions'
        ]);
        $user->roles()->attach($adminRole);
        return $user;
    }
}

