<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DataController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::get('/data', [DataController::class, 'index']);
Route::post('/data', [DataController::class, 'store']);
Route::get('/data/{username}', [DataController::class, 'show']);
Route::put('/data/{username}', [DataController::class, 'update']);
Route::delete('/data/{username}', [DataController::class, 'destroy']);
