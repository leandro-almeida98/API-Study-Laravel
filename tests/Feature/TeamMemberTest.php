<?php

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;

test('membro pode listar membros da equipe', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'member',
    ]);
    
    TeamMember::factory()->count(3)->create([
        'team_id' => $team->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/teams/{$team->id}/members");

    $response->assertStatus(200)
        ->assertJsonCount(4, 'data'); // 3 + o próprio usuário
});

test('não membro não pode listar membros', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/teams/{$team->id}/members");

    $response->assertStatus(403);
});

test('owner pode adicionar membro', function () {
    $user = User::factory()->create();
    $newMember = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id]);
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'owner',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/teams/{$team->id}/members", [
            'user_id' => $newMember->id,
            'role' => 'member',
        ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('team_members', [
        'team_id' => $team->id,
        'user_id' => $newMember->id,
        'role' => 'member',
    ]);
});

test('admin pode adicionar membro', function () {
    $user = User::factory()->create();
    $newMember = User::factory()->create();
    $team = Team::factory()->create();
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'admin',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/teams/{$team->id}/members", [
            'user_id' => $newMember->id,
            'role' => 'member',
        ]);

    $response->assertStatus(201);
});

test('membro comum não pode adicionar membro', function () {
    $user = User::factory()->create();
    $newMember = User::factory()->create();
    $team = Team::factory()->create();
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'member',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/teams/{$team->id}/members", [
            'user_id' => $newMember->id,
            'role' => 'member',
        ]);

    $response->assertStatus(403);
});

test('não pode adicionar mesmo usuário duas vezes', function () {
    $user = User::factory()->create();
    $newMember = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id]);
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'owner',
    ]);
    
    $team->teamMembers()->create([
        'user_id' => $newMember->id,
        'role' => 'member',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/teams/{$team->id}/members", [
            'user_id' => $newMember->id,
            'role' => 'admin',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['user_id']);
});

test('owner pode atualizar role de membro', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id]);
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'owner',
    ]);
    
    $teamMember = $team->teamMembers()->create([
        'user_id' => $member->id,
        'role' => 'member',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/members/{$teamMember->id}", [
            'role' => 'admin',
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('team_members', [
        'id' => $teamMember->id,
        'role' => 'admin',
    ]);
});

test('usuário não pode alterar própria role', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id]);
    
    $teamMember = $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'owner',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/members/{$teamMember->id}", [
            'role' => 'admin',
        ]);

    $response->assertStatus(403);
});

test('owner pode remover membro', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id]);
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'owner',
    ]);
    
    $teamMember = $team->teamMembers()->create([
        'user_id' => $member->id,
        'role' => 'member',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/members/{$teamMember->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('team_members', [
        'id' => $teamMember->id,
    ]);
});

test('não pode remover owner da equipe', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id]);
    
    $ownerMember = $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'owner',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/members/{$ownerMember->id}");

    $response->assertStatus(403);
});

test('admin pode remover membro comum', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'admin',
    ]);
    
    $teamMember = $team->teamMembers()->create([
        'user_id' => $member->id,
        'role' => 'member',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/members/{$teamMember->id}");

    $response->assertStatus(200);
});

test('membro comum não pode remover outros membros', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();
    
    $team->teamMembers()->create([
        'user_id' => $user->id,
        'role' => 'member',
    ]);
    
    $teamMember = $team->teamMembers()->create([
        'user_id' => $member->id,
        'role' => 'member',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/members/{$teamMember->id}");

    $response->assertStatus(403);
});