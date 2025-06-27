<?php

use Illuminate\Support\Facades\Route;

include __DIR__ . '/api/auth.php';
include __DIR__ . '/api/workshops.php';
include __DIR__ . '/api/administration.php';

Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});
