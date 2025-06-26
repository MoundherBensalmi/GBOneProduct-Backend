<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')])) {
            $user = Auth::user();
            $token = $user->createToken('GBOneProduct')->plainTextToken;

            return $this->sendResponse([
                'user' => $user,
                'token' => $token,
            ], 'user_successfully_logged_in');
        } else {
            return $this->sendError('wrong_credentials', ['error' => 'wrong_credentials'], 401);
        }
    }

    public function me(): JsonResponse
    {
        /** @var Person $user */
        $user = Auth::guard('sanctum')->user();

        return $this->sendResponse([
            'user' => $user,
        ]);
    }
}
