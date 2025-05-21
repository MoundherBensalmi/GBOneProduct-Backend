<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('me', [AuthController::class, 'me'])->middleware('gb-auth');
});
