<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('usuário pode fazer login', function () {
    $user = User::factory()->create([
        'email' => 'teste@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'teste@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'token',
            ],
        ]);
});

test('login falha com credenciais inválidas', function () {
    $user = User::factory()->create([
        'email' => 'teste@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'teste@example.com',
        'password' => 'senha-errada',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('usuário pode fazer logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)
        ->postJson('/api/v1/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Logout realizado com sucesso!']);
});