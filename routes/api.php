<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

include __DIR__ . '/api/auth.php';

Route::get('/user', function (Request $request) {
    /** @var User $user */
    $user = Auth::guard('sanctum')->user();
    $user->load('person');

    return response()->json($user);
})->middleware('gb-auth');
