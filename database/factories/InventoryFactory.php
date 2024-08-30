<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class InventoryFactory extends Factory
{
    public function definition()
    {
        $capacity = $this->faker->numberBetween(1, 40);  
        $current_stock = $this->faker->numberBetween(0, $capacity);

        return [
            'capacity' => $capacity,
            'current_stock' => $current_stock,
            'location' => $this->faker->address(),
        ];
    }
}

