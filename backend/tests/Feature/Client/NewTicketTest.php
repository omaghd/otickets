<?php

use App\Models\Category;
use App\Models\Department;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Notification::fake();
});

test('client can create a new ticket', function () {
    $client = User::factory()->client()->create();

    $department = Department::factory()->create();
    $category   = Category::factory()->create([
        'department_id' => $department->id,
    ]);

    Sanctum::actingAs($client);

    $response = $this->postJson(route('tickets.store'), [
        'subject'     => fake()->sentence(),
        'description' => fake()->paragraph(),
        'category_id' => $category->id,
        'priority'    => 'medium',
    ]);

    $id = $response->json('data.id');

    $this->assertDatabaseHas('tickets', [
        'id'        => $id,
        'client_id' => $client->id,
    ]);
});
