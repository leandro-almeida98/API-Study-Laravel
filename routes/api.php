<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::prefix('v1')->group(function () {
    Route::post('/register', RegisterController::class)->name('auth.register');
    Route::post('/login', LoginController::class)->name('auth.login');
});

// Rotas protegidas
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', LogoutController::class)->name('auth.logout');
    
    Route::get('/me', function (Request $request) {
        return response()->json([
            'data' => new \App\Http\Resources\UserResource($request->user()),
        ]);
    })->name('auth.me');
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API está funcionando',
        'timestamp' => now()->toISOString(),
    ]);
});