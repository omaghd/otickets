<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CannedResponseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => fake()->unique()->sentence(),
            'content'     => fake()->unique()->paragraph(),
            'category_id' => Category::query()->inRandomOrder()->first()->id,
        ];
    }
}
