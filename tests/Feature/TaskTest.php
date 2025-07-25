<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('usuário pode listar suas tarefas', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    
    Task::factory()->count(3)->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);
    
    Task::factory()->count(2)->create(); // Tarefas de outros usuários

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/tasks');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('usuário pode criar uma tarefa', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/tasks', [
            'project_id' => $project->id,
            'title' => 'Nova Tarefa',
            'description' => 'Descrição da tarefa',
            'status' => 'todo',
            'priority' => 'high',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'estimated_hours' => 5,
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'status',
                'priority',
            ],
        ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Nova Tarefa',
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);
});

test('criação de tarefa requer título', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/tasks', [
            'project_id' => $project->id,
            'description' => 'Descrição',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

test('criação de tarefa requer projeto válido', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/tasks', [
            'project_id' => 9999,
            'title' => 'Nova Tarefa',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['project_id']);
});

test('usuário pode visualizar tarefa que criou', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
            ],
        ]);
});

test('usuário pode visualizar tarefa atribuída a ele', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $otherUser->id,
        'assigned_to' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(200);
});

test('usuário não pode visualizar tarefa de outro usuário', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $otherUser->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(403);
});

test('usuário pode atualizar tarefa que criou', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
        'status' => 'todo',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Tarefa Atualizada',
            'status' => 'done',
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Tarefa Atualizada',
        'status' => 'done',
    ]);
});

test('usuário atribuído pode atualizar status da tarefa', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $otherUser->id,
        'assigned_to' => $user->id,
        'status' => 'todo',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/tasks/{$task->id}", [
            'status' => 'in_progress',
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'in_progress',
    ]);
});

test('usuário pode deletar tarefa que criou', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(200);

    $this->assertSoftDeleted('tasks', [
        'id' => $task->id,
    ]);
});

test('dono do projeto pode deletar qualquer tarefa do projeto', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $otherUser->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(200);
});

test('usuário não pode deletar tarefa de outro usuário', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'created_by' => $otherUser->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(403);
});

test('pode filtrar tarefas por status', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    
    Task::factory()->count(2)->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
        'status' => 'todo',
    ]);
    
    Task::factory()->count(3)->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
        'status' => 'done',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/tasks?status=todo');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('pode filtrar tarefas por prioridade', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    
    Task::factory()->count(2)->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
        'priority' => 'high',
    ]);
    
    Task::factory()->count(3)->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
        'priority' => 'low',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/tasks?priority=high');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('pode filtrar tarefas por projeto', function () {
    $user = User::factory()->create();
    $project1 = Project::factory()->create(['user_id' => $user->id]);
    $project2 = Project::factory()->create(['user_id' => $user->id]);
    
    Task::factory()->count(3)->create([
        'project_id' => $project1->id,
        'created_by' => $user->id,
    ]);
    
    Task::factory()->count(2)->create([
        'project_id' => $project2->id,
        'created_by' => $user->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/tasks?project_id={$project1->id}");

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('pode listar apenas tarefas atribuídas ao usuário', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    
    Task::factory()->count(2)->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
        'assigned_to' => $user->id,
    ]);
    
    Task::factory()->count(3)->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
        'assigned_to' => $otherUser->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/tasks?assigned_to_me=1');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('pode listar tarefas atrasadas', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    
    Task::factory()->count(2)->overdue()->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
    ]);
    
    Task::factory()->count(3)->create([
        'project_id' => $project->id,
        'created_by' => $user->id,
        'due_date' => now()->addDays(5),
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/tasks?overdue=1');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('usuário não autenticado não pode acessar tarefas', function () {
    $response = $this->getJson('/api/v1/tasks');

    $response->assertStatus(401);
});
    