<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketReplyFactory extends Factory
{
    public function definition(): array
    {
        $user          = User::query()->inRandomOrder()->whereIn('role', ['agent', 'client'])->first();
        $isClientReply = $user->isClient();

        return [
            'content'         => fake()->paragraph(),
            'ticket_id'       => Ticket::query()->inRandomOrder()->first()->id,
            'user_id'         => $user->id,
            'is_client_reply' => $isClientReply,
        ];
    }
}
