<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Data;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $users = Data::getAllData();

        $user = $users->firstWhere('username', $credentials['username']);

        if (!$user || $user->password !== $credentials['password']) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        try {
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'Could not create token'], 500);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(['token' => $token]);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Logged out']);
    }
}
