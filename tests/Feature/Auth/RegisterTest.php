<?php

use App\Models\User;

test('usuário pode se registrar', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'Teste User',
        'email' => 'teste@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'user' => ['id', 'name', 'email'],
                'token',
            ],
        ]);

    expect(User::where('email', 'teste@example.com')->exists())->toBeTrue();
});

test('registro requer nome', function () {
    $response = $this->postJson('/api/v1/register', [
        'email' => 'teste@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('registro requer email válido', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'Teste',
        'email' => 'email-invalido',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('registro requer confirmação de senha', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'Teste',
        'email' => 'teste@example.com',
        'password' => 'password123',
        'password_confirmation' => 'senha-diferente',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});