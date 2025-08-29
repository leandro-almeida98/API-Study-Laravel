<?php

use App\Models\Team;
use App\Models\User;

test('usuário pode listar suas equipes', function () {
    $user = User::factory()->create();
    
    // Equipes que o usuário é owner
    $ownedTeams = Team::factory()->count(2)->create(['owner_id' => $user->id]);
    
    // Adicionar usuário como membro em cada equipe
    foreach ($ownedTeams as $team) {
        $team->teamMembers()->create([
            'user_id' => $user->id,
            'role' => 'owner',
        ]);
    }
    
    // Equipe de outro usuário
    Team::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/teams');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('usuário pode criar uma equipe', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/teams', [
            'name' => 'Equipe de Desenvolvimento',
            'description' => 'Time de backend',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'owner',
            ],
        ]);

    $this->assertDatabaseHas('teams', [
        'name' => 'Equipe de Desenvolvimento',
        'owner_id' => $user->id,
    ]);

    // Verificar se o criador foi adicionado como owner
    $this->assertDatabaseHas('team_members', [
        'user_id' => $user->id,
        'role' => 'owner',
    ]);
});

test('criação de equipe requer nome', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/teams', [
            'description' => 'Descrição',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('membro pode visualizar equipe', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'member',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/teams/{$team->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $team->id,
                'name' => $team->name,
            ],
        ]);
});

test('não membro não pode visualizar equipe', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/teams/{$team->id}");

    $response->assertStatus(403);
});

test('owner pode atualizar equipe', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id]);
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'owner',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/teams/{$team->id}", [
            'name' => 'Equipe Atualizada',
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('teams', [
        'id' => $team->id,
        'name' => 'Equipe Atualizada',
    ]);
});

test('membro comum não pode atualizar equipe', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'member',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/teams/{$team->id}", [
            'name' => 'Tentativa de Atualização',
        ]);

    $response->assertStatus(403);
});

test('apenas owner pode deletar equipe', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id]);
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'owner',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/teams/{$team->id}");

    $response->assertStatus(200);

    $this->assertSoftDeleted('teams', [
        'id' => $team->id,
    ]);
});

test('admin não pode deletar equipe', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'admin',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/teams/{$team->id}");

    $response->assertStatus(403);
});

test('usuário não autenticado não pode acessar equipes', function () {
    $response = $this->getJson('/api/v1/teams');

    $response->assertStatus(401);
});