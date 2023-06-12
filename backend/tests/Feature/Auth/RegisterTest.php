<?php

test('register a client', function () {
    $response = $this->post(route('register'), [
        'name'                  => fake()->name(),
        'email'                 => fake()->email(),
        'phone'                 => fake()->phoneNumber(),
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $id = $response->json('data.user.id');

    $this->assertDatabaseHas('users', [
        'id'   => $id,
        'role' => 'client',
    ]);
});
