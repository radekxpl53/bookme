<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'name' => fake()->company() . ' Salon',
            'address' => fake()->address(),
            'lon' => fake()->longitude(14.0, 24.0),
            'lat' => fake()->latitude(49.0, 54.0),
            'description' => fake()->realText(200),
        ];
    }
}
