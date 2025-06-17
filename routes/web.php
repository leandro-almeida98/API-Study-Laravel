<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return response()->json([
        'message' => 'Task Manager API',
        'status' => 'running',
        'version' => '1.0.0',
    ]);
});

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Conexão com banco de dados OK!',
            'database' => $dbName,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Erro ao conectar com banco de dados',
            'error' => $e->getMessage(),
        ], 500);
    }
});
