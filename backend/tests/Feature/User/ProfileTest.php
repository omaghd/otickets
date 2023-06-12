<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;

test('a user can update their profile', function () {
    $user = User::factory()
        ->create([
            'role' => fake()->randomElement(['admin', 'agent', 'client'])
        ]);

    $this->actingAs($user)
        ->putJson(route('update-profile'), [
            'name'  => fake()->name(),
            'email' => fake()->email(),
        ])
        ->assertOk();
});

test('a user can update photo profile', function () {
    $user = User::factory()
        ->create([
            'role' => fake()->randomElement(['admin', 'agent', 'client'])
        ]);

    $profilePicture = UploadedFile::fake()->image('photo.jpg');

    $request = $this->actingAs($user)
        ->putJson(route('update-profile'), [
            'picture' => $profilePicture,
        ]);

    $folder   = $user->isClient() ? 'clients' : 'users';
    $filePath = "{$folder}/{$user->id}/profile-pictures/{$profilePicture->hashName()}";
    Storage::disk('public')->assertExists($filePath);

    $this->assertDatabaseHas('users', [
        'id'      => $user->id,
        'picture' => "storage/{$filePath}",
    ]);

    $request->assertOk();

    $user->isClient()
        ? Storage::disk('public')->deleteDirectory("clients")
        : Storage::disk('public')->deleteDirectory("users");
});
