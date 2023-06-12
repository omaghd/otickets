<?php

use App\Models\Category;
use App\Models\Department;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Notification::fake();
});

test('an agent can create a ticket on behalf of a client', function () {
    $department = Department::factory()->create();

    $agent = User::factory()->agent()->create([
        'department_id' => $department->id,
    ]);

    $category = Category::factory()->create([
        'department_id' => $department->id,
    ]);

    Sanctum::actingAs($agent);

    $client = User::factory()->client()->create();

    $response = $this->postJson(route('tickets.store'), [
        'subject'     => fake()->sentence(),
        'description' => fake()->paragraph(),
        'category_id' => $category->id,
        'priority'    => 'medium',
        'client_id'   => $client->id,
    ]);

    $ticketId = $response->json('data.id');

    $this->assertDatabaseHas('tickets', [
        'id'        => $ticketId,
        'client_id' => $client->id,
    ]);

    $this->assertDatabaseHas('agents_tickets', [
        'agent_id'  => $agent->id,
        'ticket_id' => $ticketId,
    ]);
});
