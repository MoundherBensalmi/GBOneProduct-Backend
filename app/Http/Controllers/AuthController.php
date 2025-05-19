<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')])) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->plainTextToken;

            return $this->sendResponse([
                'user' => $user,
                'token' => $token,
            ], 'user_successfully_logged_in');
        } else {
            return $this->sendError('wrong_credentials.', ['error' => 'wrong_credentials']);
        }
    }
}
