<?php

use App\Models\CannedResponse;
use App\Models\Category;
use App\Models\Department;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

test('agent gets the appropriate canned responses', function () {
    $department        = Department::factory()->create();
    $anotherDepartment = Department::factory()->create();

    $agent = User::factory()->agent()->create([
        'department_id' => $department->id,
    ]);

    $category = Category::factory()->create([
        'department_id' => $department->id,
    ]);

    $anotherCategory = Category::factory()->create([
        'department_id' => $anotherDepartment->id,
    ]);

    CannedResponse::factory()->count(3)->create([
        'category_id' => $category->id,
    ]);

    // Create 3 canned responses without an agent assigned
    CannedResponse::factory()->count(3)->create([
        'category_id' => $category->id,
        'agent_id'    => null,
    ]);

    // Create 3 canned responses with another category and without an agent assigned
    CannedResponse::factory()->count(3)->create([
        'category_id' => $anotherCategory->id,
        'agent_id'    => null,
    ]);

    Sanctum::actingAs($agent);

    $response = $this->get(route('canned-responses.index'));

    $response->assertJson(
        fn(AssertableJson $json) => $json
            ->has('data', 6)
            ->etc()
    );

    $response->assertJsonMissing([
        'data' => [
            [
                'category_id' => $anotherCategory->id,
            ],
        ],
    ]);
});

test("agent can't edit canned responses that don't belong to them", function () {
    $department = Department::factory()->create();

    $agent = User::factory()->agent()->create([
        'department_id' => $department->id,
    ]);

    $anotherAgent = User::factory()->agent()->create([
        'department_id' => $department->id,
    ]);

    $category = Category::factory()->create([
        'department_id' => $department->id,
    ]);

    $cannedResponse = CannedResponse::factory()->create([
        'category_id' => $category->id,
        'agent_id'    => $anotherAgent->id,
    ]);

    // another canned response without an agent assigned
    $anotherCannedResponse = CannedResponse::factory()->create([
        'category_id' => $category->id,
    ]);

    Sanctum::actingAs($agent);

    $response = $this->putJson(route('canned-responses.update', $cannedResponse->id), [
        'title'   => 'New title',
        'content' => 'New content',
    ]);

    $response->assertForbidden();

    $response = $this->putJson(route('canned-responses.update', $anotherCannedResponse->id), [
        'title'   => 'New title',
        'content' => 'New content',
    ]);

    $response->assertForbidden();
});
