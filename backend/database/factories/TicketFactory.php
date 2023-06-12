<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        $ticketService = app(TicketService::class);

        return [
            'reference'   => $ticketService->assignRandomReference(),
            'subject'     => ucfirst(fake()->unique()->words(asText: true)),
            'description' => fake()->paragraph(),
            'priority'    => fake()->randomElement(['low', 'medium', 'high']),
            'category_id' => Category::query()->inRandomOrder()->first()->id,
            'client_id'   => User::query()->inRandomOrder()->where('role', 'client')->first()->id,
        ];
    }
}
