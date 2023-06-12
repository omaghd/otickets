<?php

it('generates a string of the specified length', function () {
    $length    = 8;
    $reference = generateTicketReference($length);
    expect(strlen($reference))->toBe($length);
});

it('generates a string with only alphanumeric characters', function () {
    $reference = generateTicketReference();
    expect(ctype_alnum($reference))->toBeTrue();
});

it('generates a string with uppercase characters only', function () {
    $reference = generateTicketReference();
    expect($reference)->toBe(strtoupper($reference));
});
