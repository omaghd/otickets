<?php

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns a token', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login'), [
        'email'    => $user->email,
        'password' => 'OTickets@00',
    ]);

    logger($response->getContent());

    $response->assertJson(
        fn(AssertableJson $json) => $json
            ->whereNot('data.token', null)
            ->etc()
    );
});
