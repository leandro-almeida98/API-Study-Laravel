<?php

use App\Models\Project;
use App\Models\User;

test('usuário autenticado pode listar seus projetos', function () {
    $user = User::factory()->create();
    Project::factory()->count(3)->create(['user_id' => $user->id]);
    Project::factory()->count(2)->create(); // Projetos de outros usuários

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/projects');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('usuário pode criar um projeto', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/projects', [
            'name' => 'Novo Projeto',
            'description' => 'Descrição do projeto',
            'status' => 'active',
            'deadline' => now()->addDays(30)->format('Y-m-d'),
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'status',
                'deadline',
            ],
        ]);

    $this->assertDatabaseHas('projects', [
        'name' => 'Novo Projeto',
        'user_id' => $user->id,
    ]);
});

test('criação de projeto requer nome', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/projects', [
            'description' => 'Descrição',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('usuário pode visualizar seu próprio projeto', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/projects/{$project->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
        ]);
});

test('usuário não pode visualizar projeto de outro usuário', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/projects/{$project->id}");

    $response->assertStatus(403);
});

test('usuário pode atualizar seu próprio projeto', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/projects/{$project->id}", [
            'name' => 'Projeto Atualizado',
            'status' => 'completed',
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Projeto Atualizado',
        'status' => 'completed',
    ]);
});

test('usuário não pode atualizar projeto de outro usuário', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/projects/{$project->id}", [
            'name' => 'Tentativa de Atualização',
        ]);

    $response->assertStatus(403);
});

test('usuário pode deletar seu próprio projeto', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/projects/{$project->id}");

    $response->assertStatus(200);

    $this->assertSoftDeleted('projects', [
        'id' => $project->id,
    ]);
});

test('usuário não pode deletar projeto de outro usuário', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/projects/{$project->id}");

    $response->assertStatus(403);
});

test('pode filtrar projetos por status', function () {
    $user = User::factory()->create();
    Project::factory()->count(2)->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);
    Project::factory()->count(3)->create([
        'user_id' => $user->id,
        'status' => 'completed',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/projects?status=active');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('pode buscar projetos por nome', function () {
    $user = User::factory()->create();
    Project::factory()->create(['user_id' => $user->id, 'name' => 'Projeto Laravel']);
    Project::factory()->create(['user_id' => $user->id, 'name' => 'Projeto React']);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/projects?search=Laravel');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('usuário não autenticado não pode acessar projetos', function () {
    $response = $this->getJson('/api/v1/projects');

    $response->assertStatus(401);
});