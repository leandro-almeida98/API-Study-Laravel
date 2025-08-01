<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;
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

    // Projetos
    Route::apiResource('projects', ProjectController::class);

    // Tarefas
    Route::apiResource('tasks', TaskController::class);

    // Comentários (nested resource)
    Route::apiResource('tasks.comments', CommentController::class)
        ->except(['update'])
        ->shallow();

    // Rota separada para update (não precisa do task_id)
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API está funcionando',
        'timestamp' => now()->toISOString(),
    ]);
});