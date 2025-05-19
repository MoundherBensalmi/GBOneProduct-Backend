<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::get('/user', function (Request $request) {
    $user = Auth::guard('sanctum')->user();
    for ($i = 0; $i < 1000; $i++) {
        $users = \App\Models\User::query()->get();
    }
    return response()->json($user);
})->middleware('gb-auth');
