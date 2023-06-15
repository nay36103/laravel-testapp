<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $users = Data::getAllData();

        $user = $users->firstWhere('username', $credentials['username']);

        if (!$user || $user->password !== $credentials['password']) {
            $this->writeToLog('Invalid credentials');
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            $this->writeToLog('Could not create token: ' . $e->getMessage());
            return response()->json(['error' => 'Could not create token'], 500);
        }

        $this->writeToLog('User logged in: ' . $user->username . ' | IP: ' . $request->ip());

        return response()->json(['token' => $token]);
    }

    private function writeToLog($message)
    {
        $logFilePath = storage_path('logs/userdata.log');
        file_put_contents($logFilePath, $message, FILE_APPEND);
    }
}
