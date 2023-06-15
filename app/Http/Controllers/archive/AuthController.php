<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Data;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $users = Data::getAllData();

        $user = $users->firstWhere('username', $credentials['username']);

        if (!$user || $user->password !== $credentials['password']) {
            Log::info('Invalid credentials');
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        try {
            if (!$token = JWTAuth::fromUser($user)) {
                Log::error('Could not create token');
                return response()->json(['error' => 'Could not create token'], 500);
            }
        } catch (JWTException $e) {
            Log::error('Could not create token: ' . $e->getMessage());
            return response()->json(['error' => 'Could not create token'], 500);
        }

        $logData = [
            'username' => $user->username,
            'timestamp' => now()->toDateTimeString(),
            'ip' => $request->ip(),
        ];
        Log::info('User logged in: ' . $user->username . ' | IP: ' . $request->ip());

        $logFile = resource_path('json/logs.json');
        $existingLogs = [];

        if (File::exists($logFile)) {
            $existingLogs = json_decode(File::get($logFile), true);
        }

        $existingLogs[] = $logData;

        File::put($logFile, json_encode($existingLogs, JSON_PRETTY_PRINT));

        return response()->json(['token' => $token]);
    }
}
