<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class OrderFactory extends Factory
{
    public function definition()
    {
        return [
            'total_price' => $this->faker->randomFloat(2, 20, 500),
            'status' => $this->faker->randomElement(['processing', 'completed', 'cancelled']),
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
