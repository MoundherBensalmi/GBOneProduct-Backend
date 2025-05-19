<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::get('/user', function (Request $request) {
    /** @var User $user */
    $user = Auth::guard('sanctum')->user();
    $user->load('person');

    return response()->json($user);
})->middleware('gb-auth');
