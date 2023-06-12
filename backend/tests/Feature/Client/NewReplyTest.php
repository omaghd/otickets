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

test("client can reply to their ticket", function () {
    $client = User::factory()->client()->create();

    $category = Category::factory()->create([
        'department_id' => Department::factory()->create()->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => $client->id,
        'category_id' => $category->id,
    ]);

    Sanctum::actingAs($client);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'reply',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $id = $response->json('data.id');

    $this->assertDatabaseHas('ticket_replies', [
        'id'              => $id,
        'ticket_id'       => $ticket->id,
        'user_id'         => $client->id,
        'is_client_reply' => true,
    ]);
});

test("client can reply to their ticket and attach a file", function () {
    $client = User::factory()->client()->create();

    $category = Category::factory()->create([
        'department_id' => Department::factory()->create()->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => $client->id,
        'category_id' => $category->id,
    ]);

    $attachments = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
    ];

    Sanctum::actingAs($client);

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
        'user_id'         => $client->id,
        'is_client_reply' => true,
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

test("client can't reply to a ticket that doesn't belong to them", function () {
    $clients = User::factory()->client()->count(2)->create();

    $firstClient  = $clients->get(0);
    $secondClient = $clients->get(1);

    $category = Category::factory()->create([
        'department_id' => Department::factory()->create()->id,
    ]);

    $ticket = Ticket::factory()->create([
        'category_id' => $category->id,
        'client_id'   => $firstClient->id,
    ]);

    Sanctum::actingAs($secondClient);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'reply',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $response->assertForbidden();
});

test("client can reply and mark their tickets as closed", function () {
    $client = User::factory()->client()->create();

    $category = Category::factory()->create([
        'department_id' => Department::factory()->create()->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => $client->id,
        'category_id' => $category->id,
    ]);

    Sanctum::actingAs($client);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'close',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $id = $response->json('data.id');

    $this->assertDatabaseHas('ticket_replies', [
        'id'              => $id,
        'ticket_id'       => $ticket->id,
        'user_id'         => $client->id,
        'is_client_reply' => true,
    ]);

    $this->assertDatabaseHas('tickets', [
        'id'     => $ticket->id,
        'status' => 'closed',
    ]);
});

test("client can't close a ticket that doesn't belong to them", function () {
    $clients = User::factory()->client()->count(2)->create();

    $firstClient  = $clients->get(0);
    $secondClient = $clients->get(1);

    $category = Category::factory()->create([
        'department_id' => Department::factory()->create()->id,
    ]);

    $ticket = Ticket::factory()->create([
        'category_id' => $category->id,
        'client_id'   => $firstClient->id,
    ]);

    Sanctum::actingAs($secondClient);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'close',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $response->assertForbidden();
});

test("client can reply and mark their ticket as resolved", function () {
    $client = User::factory()->client()->create();

    $category = Category::factory()->create([
        'department_id' => Department::factory()->create()->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => $client->id,
        'category_id' => $category->id,
    ]);

    Sanctum::actingAs($client);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'resolve',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $id = $response->json('data.id');

    $this->assertDatabaseHas('ticket_replies', [
        'id'              => $id,
        'ticket_id'       => $ticket->id,
        'user_id'         => $client->id,
        'is_client_reply' => true,
    ]);

    $this->assertDatabaseHas('tickets', [
        'id'     => $ticket->id,
        'status' => 'resolved',
    ]);
});

test("client can't resolve a ticket that doesn't belong to them", function () {
    $clients = User::factory()->client()->count(2)->create();

    $firstClient  = $clients->get(0);
    $secondClient = $clients->get(1);

    $category = Category::factory()->create([
        'department_id' => Department::factory()->create()->id,
    ]);

    $ticket = Ticket::factory()->create([
        'category_id' => $category->id,
        'client_id'   => $firstClient->id,
    ]);

    Sanctum::actingAs($secondClient);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'resolve',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $response->assertForbidden();
});

test("client can't reply to a resolved ticket", function () {
    $client = User::factory()->client()->create();

    $category = Category::factory()->create([
        'department_id' => Department::factory()->create()->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => $client->id,
        'category_id' => $category->id,
        'status'      => 'resolved',
    ]);

    Sanctum::actingAs($client);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'reply',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $response->assertForbidden();
});

test("client can't reply to a closed ticket", function () {
    $client = User::factory()->client()->create();

    $category = Category::factory()->create([
        'department_id' => Department::factory()->create()->id,
    ]);

    $ticket = Ticket::factory()->create([
        'client_id'   => $client->id,
        'category_id' => $category->id,
        'status'      => 'closed',
    ]);

    Sanctum::actingAs($client);

    $response = $this->postJson(route('ticket-replies.store'), [
        'action'    => 'reply',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    $response->assertForbidden();
});

test("client get notified when their ticket is replied to", function () {
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

    Sanctum::actingAs($agent);

    $this->postJson(route('ticket-replies.store'), [
        'action'    => 'reply',
        'ticket_id' => $ticket->id,
        'content'   => fake()->paragraph(),
    ]);

    Notification::assertSentTo(
        $client,
        NewReply::class,
        fn($notification, $channels) => $notification->ticket->id === $ticket->id
            && in_array('mail', $channels)
            && in_array('database', $channels)
    );
});

