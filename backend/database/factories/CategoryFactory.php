<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'          => fake()->unique()->words(2, true),
            'slug'          => fake()->unique()->slug(3),
            'department_id' => Department::query()->inRandomOrder()->first()->id,
        ];
    }
}
