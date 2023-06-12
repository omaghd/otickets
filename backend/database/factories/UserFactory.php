<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'phone'             => fake()->phoneNumber(),
            'email_verified_at' => now(),
            'password'          => 'OTickets@00',
            'remember_token'    => Str::random(10),
        ];
    }

    public function admin(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function agent(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'role'          => 'agent',
            'department_id' => Department::query()->inRandomOrder()->first()->id,
        ]);
    }

    public function client(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'client',
        ]);
    }
}
