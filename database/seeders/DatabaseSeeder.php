<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Inventory;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(8)->create();        
        Customer::factory(8)->create();        
        Order::factory(8)->create();
        Category::factory(8)->create();
        Supplier::factory(8)->create();
        Product::factory(8)->create();
        Inventory::factory(8)->create();
        $this->call(RolePermissionSeeder::class);
    }
}
