<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function index()
    {
        return json_encode([
            'message' => 'Hello, World!',
        ]);
    }
}
