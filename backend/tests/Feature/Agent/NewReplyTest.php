<?php

use App\Models\Category;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\NewReply;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Notification::fake();
});

test('agent can reply to a ticket they are assigned to', function () {
    $department = Department::factory()->create();

    $agent = User::factory()->agent()->create([
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

    $ticket->agents()->save($agent, ['is_current' => true]);

    Sanctum::actingAs($agent);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'reply',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $id = $response->json('data.id');

    logger($response->json());

    $this->assertDatabaseHas('ticket_replies', [
        'id'              => $id,
        'ticket_id'       => $ticket->id,
        'user_id'         => $agent->id,
        'is_client_reply' => false,
    ]);
});

test('agent can reply to a ticket they are assigned to and attach a file', function () {
    $department = Department::factory()->create();

    $agent = User::factory()->agent()->create([
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

    $ticket->agents()->save($agent, ['is_current' => true]);

    Sanctum::actingAs($agent);

    $attachments = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
    ];

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'      => 'reply',
        'ticket_id'   => $ticket->id,
        'content'     => fake()->paragraph(),
        'attachments' => $attachments,
    ]);

    $id = $response->json('data.id');

    $this->assertDatabaseHas('ticket_replies', [
        'id'              => $id,
        'ticket_id'       => $ticket->id,
        'user_id'         => $agent->id,
        'is_client_reply' => false,
    ]);

    foreach ($attachments as $attachment) {
        $filePath = "attachments/tickets/{$ticket->reference}/replies/{$attachment->hashName()}";
        Storage::disk('public')->assertExists($filePath);

        $filename = pathinfo(
            $attachment->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        $this->assertDatabaseHas('reply_attachments', [
            'ticket_reply_id' => $response->json('data.id'),
            'filename'        => $filename,
        ]);
    }

    Storage::disk('public')->deleteDirectory("attachments");
});

test("agent can't reply to a ticket they are not assigned to", function () {
    $department = Department::factory()->create();

    $agents = User::factory()->agent()->count(2)->create([
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

    $ticket->agents()->save($agents[0], ['is_current' => true]);

    Sanctum::actingAs($agents[1]);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'reply',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $response->assertForbidden();
});

test("agent can reply and mark a ticket they are assigned to as closed", function () {
    $department = Department::factory()->create();

    $agent = User::factory()->agent()->create([
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

    $ticket->agents()->save($agent, ['is_current' => true]);

    Sanctum::actingAs($agent);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'close',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $id = $response->json('data.id');

    $this->assertDatabaseHas('ticket_replies', [
        'id'              => $id,
        'ticket_id'       => $ticket->id,
        'user_id'         => $agent->id,
        'is_client_reply' => false,
    ]);

    $this->assertDatabaseHas('tickets', [
        'id'     => $ticket->id,
        'status' => 'closed',
    ]);
});

test("agent can't close a ticket that doesn't belong to them", function () {
    $department = Department::factory()->create();

    $agents = User::factory()->agent()->count(2)->create([
        'department_id' => $department->id,
    ]);

    $category = Category::factory()->create([
        'department_id' => $department->id,
    ]);

    $ticket = Ticket::factory()->create([
        'category_id' => $category->id,
        'client_id'   => User::factory()->client()->create()->id,
    ]);

    $ticket->agents()->save($agents[0], ['is_current' => true]);

    Sanctum::actingAs($agents[1]);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'close',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $response->assertForbidden();
});

test("agent can reply and mark their ticket as resolved", function () {
    $department = Department::factory()->create();

    $agent = User::factory()->agent()->create([
        'department_id' => $department->id,
    ]);

    $category = Category::factory()->create([
        'department_id' => $department->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => User::factory()->client()->create()->id,
        'category_id' => $category->id,
    ]);

    $ticket->agents()->save($agent, ['is_current' => true]);

    Sanctum::actingAs($agent);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'resolve',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $id = $response->json('data.id');

    $this->assertDatabaseHas('ticket_replies', [
        'id'              => $id,
        'ticket_id'       => $ticket->id,
        'user_id'         => $agent->id,
        'is_client_reply' => false,
    ]);

    $this->assertDatabaseHas('tickets', [
        'id'     => $ticket->id,
        'status' => 'resolved',
    ]);
});

test("client can't resolve a ticket that doesn't belong to them", function () {
    $department = Department::factory()->create();

    $agents = User::factory()->agent()->count(2)->create([
        'department_id' => $department->id,
    ]);

    $category = Category::factory()->create([
        'department_id' => $department->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => User::factory()->client()->create()->id,
        'category_id' => $category->id,
    ]);

    $ticket->agents()->save($agents[0], ['is_current' => true]);

    Sanctum::actingAs($agents[1]);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'resolve',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $response->assertForbidden();
});

test("agent can't reply to a resolved ticket", function () {
    $department = Department::factory()->create();

    $agent = User::factory()->agent()->create([
        'department_id' => $department->id,
    ]);

    $category = Category::factory()->create([
        'department_id' => $department->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => User::factory()->client()->create()->id,
        'category_id' => $category->id,
        'status'      => 'resolved',
    ]);

    $ticket->agents()->save($agent, ['is_current' => true]);

    Sanctum::actingAs($agent);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'reply',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $response->assertForbidden();
});

test("agent can't reply to a closed ticket", function () {
    $department = Department::factory()->create();

    $agent = User::factory()->agent()->create([
        'department_id' => $department->id,
    ]);

    $category = Category::factory()->create([
        'department_id' => $department->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => User::factory()->client()->create()->id,
        'category_id' => $category->id,
        'status'      => 'closed',
    ]);

    $ticket->agents()->save($agent, ['is_current' => true]);

    Sanctum::actingAs($agent);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'reply',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $response->assertForbidden();
});

test("agent get notified when their ticket is replied to", function () {
    $client = User::factory()->client()->create();

    $category = Category::factory()->create([
        'department_id' => Department::factory()->create()->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => $client->id,
        'category_id' => $category->id,
    ]);

    $agent = User::factory()->agent()->create();

    $ticket->agents()->save($agent, [
        'transferred_by' => $agent->id,
        'is_current'     => true,
    ]);

    Sanctum::actingAs($client);

    $this->postJson(route('ticket-replies.store'), [
        'action'    => 'reply',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    Notification::assertSentTo(
        $agent,
        NewReply::class,
        fn($notification, $channels) => $notification->ticket->id === $ticket->id
            && in_array('mail', $channels)
            && in_array('database', $channels)
    );
});
