<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->firstName() . ' ' . fake()->lastName(),
            'photo' => null,
            'specialization' => fake()->randomElement(['Fryzjer', 'Barber', 'Kosmetyczka', 'Masażysta']),
            'is_active' => true,
        ];
    }
}
