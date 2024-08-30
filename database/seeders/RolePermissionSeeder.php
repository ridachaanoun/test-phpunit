<?php


namespace Database\Seeders;
// database/seeders/RolePermissionSeeder.php
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['label' => 'create', 'description' => 'Permission to create resources'],
            ['label' => 'edit', 'description' => 'Permission to edit resources'],
            ['label' => 'delete', 'description' => 'Permission to delete resources'],
            // Add other permissions as needed
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Ensure the Admin role exists
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'description' => 'Administrator role with full permissions'
        ]);
    }
}


