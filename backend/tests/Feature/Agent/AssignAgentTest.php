<?php

use App\Models\Category;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('agent can assign a ticket to themselves', function () {
    $department = Department::factory()->create();

    $agent  = User::factory()->agent()->create([
        'department_id' => $department->id,
    ]);

    $client = User::factory()->client()->create();

    $category = Category::factory()->create([
        'department_id' => $department->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => $client->id,
        'category_id' => $category->id,
    ]);

    Sanctum::actingAs($agent);

    $response = $this->putJson(route('assign-agent'), [
        'ticket_id'   => $ticket->id,
        'agent_id'    => $agent->id,
        'transfer_to' => 'me',
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('agents_tickets', [
        'ticket_id' => $ticket->id,
        'agent_id'  => $agent->id,
    ]);
});
