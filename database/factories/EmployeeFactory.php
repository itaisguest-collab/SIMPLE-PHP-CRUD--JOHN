<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'first_name'   => $this->faker->firstName,
            'last_name'    => $this->faker->lastName,
            'gender'       => $this->faker->randomElement(['Male','Female','Other']),
            'date_of_birth'=> $this->faker->date('Y-m-d', '-18 years'),
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}