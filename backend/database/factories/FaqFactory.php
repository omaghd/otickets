<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaqFactory extends Factory
{
    public function definition(): array
    {
        $question = ucfirst(fake()->unique()->words(asText: true));
        $slug     = str()->slug($question);
        return [
            'question'     => $question,
            'slug'         => $slug,
            'answer'       => fake()->unique()->paragraphs(asText: true),
            'excerpt'      => fake()->unique()->paragraph(2),
            'is_published' => true,
            'category_id'  => Category::query()->inRandomOrder()->first()->id,
        ];
    }
}
