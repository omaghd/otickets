<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'name'    => config('app.name'),
        'version' => '1.0.0',
        'author'  => 'OmaghD',
        'email'   => 'contact@omaghd.com',
        'website' => 'https://omaghd.com',
    ];
});
