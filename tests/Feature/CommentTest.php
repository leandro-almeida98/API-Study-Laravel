<?php

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('usuário pode listar comentários de uma tarefa que tem acesso', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);
    
    Comment::factory()->count(5)->create([
        'task_id' => $task->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/tasks/{$task->id}/comments");

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data');
});

test('usuário não pode listar comentários de tarefa sem acesso', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $otherUser->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/tasks/{$task->id}/comments");

    $response->assertStatus(403);
});

test('usuário pode criar comentário em tarefa que tem acesso', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/tasks/{$task->id}/comments", [
            'content' => 'Ótimo trabalho!',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'content',
                'user',
            ],
        ]);

    $this->assertDatabaseHas('comments', [
        'task_id' => $task->id,
        'user_id' => $user->id,
        'content' => 'Ótimo trabalho!',
    ]);
});

test('criação de comentário requer conteúdo', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/tasks/{$task->id}/comments", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['content']);
});

test('usuário não pode comentar em tarefa sem acesso', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $otherUser->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/tasks/{$task->id}/comments", [
            'content' => 'Tentando comentar',
        ]);

    $response->assertStatus(403);
});

test('usuário atribuído pode comentar na tarefa', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $otherUser->id,
        'assigned_to' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/tasks/{$task->id}/comments", [
            'content' => 'Trabalhando nisso!',
        ]);

    $response->assertStatus(201);
});

test('usuário pode visualizar comentário específico', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/comments/{$comment->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $comment->id,
                'content' => $comment->content,
            ],
        ]);
});

test('usuário pode atualizar seu próprio comentário', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/comments/{$comment->id}", [
            'content' => 'Comentário atualizado',
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'content' => 'Comentário atualizado',
    ]);
});

test('usuário não pode atualizar comentário de outro usuário', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $otherUser->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/comments/{$comment->id}", [
            'content' => 'Tentando atualizar',
        ]);

    $response->assertStatus(403);
});

test('usuário pode deletar seu próprio comentário', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/comments/{$comment->id}");

    $response->assertStatus(200);

    $this->assertSoftDeleted('comments', [
        'id' => $comment->id,
    ]);
});

test('dono do projeto pode deletar qualquer comentário', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $otherUser->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/comments/{$comment->id}");

    $response->assertStatus(200);
});

test('usuário não pode deletar comentário de outro usuário', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $thirdUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $otherUser->id,
        'assigned_to' => $user->id,
    ]);
    $comment = Comment::factory()->create([
        'task_id' => $task->id,
        'user_id' => $thirdUser->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/comments/{$comment->id}");

    $response->assertStatus(403);
});

test('usuário não autenticado não pode acessar comentários', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/v1/tasks/{$task->id}/comments");

    $response->assertStatus(401);
});
