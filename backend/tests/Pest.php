<?php

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Unit', 'Feature');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
