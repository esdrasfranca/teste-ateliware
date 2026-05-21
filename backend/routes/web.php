<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    header('Content-Type: application/json');
    return json_encode([
        'message' => 'Hello, World!',
    ]);
});
