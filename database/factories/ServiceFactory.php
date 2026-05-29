<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->randomElement(['Strzyżenie męskie', 'Strzyżenie damskie', 'Modelowanie', 'Masaż relaksacyjny', 'Manicure hybrydowy']),
            'price' => fake()->randomFloat(2, 40, 300),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90, 120]),
        ];
    }
}
