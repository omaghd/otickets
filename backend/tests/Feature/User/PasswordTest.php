AssignAgentTest.php<?php

use App\Models\User;
use App\Notifications\PasswordChanged;

beforeEach(function () {
    Notification::fake();
});

test('a user can change their password', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->putJson(route('update-password'), [
            'current_password'      => 'OTickets@00',
            'password'              => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertOk();

    $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
});

test('a user get notified when their password is changed', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->putJson(route('update-password'), [
            'current_password'      => 'OTickets@00',
            'password'              => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertOk();

    Notification::assertSentTo($user, PasswordChanged::class);
});
