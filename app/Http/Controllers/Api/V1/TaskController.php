<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * Lista todas as tarefas acessíveis ao usuário
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $tasks = Task::with(['project', 'assignedTo', 'createdBy'])
            ->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id)
                      ->orWhereHas('project', function ($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            })
            ->when($request->status, fn($q) => $q->byStatus($request->status))
            ->when($request->priority, fn($q) => $q->byPriority($request->priority))
            ->when($request->project_id, fn($q) => $q->byProject($request->project_id))
            ->when($request->assigned_to_me, fn($q) => $q->assignedToUser($user->id))
            ->when($request->overdue, fn($q) => $q->overdue())
            ->latest()
            ->paginate(15);

        return TaskResource::collection($tasks);
    }

    /**
     * Cria uma nova tarefa
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $task = Task::create([
            ...$request->validated(),
            'created_by' => $user->id,
        ]);

        $task->load(['project', 'assignedTo', 'createdBy']);

        return response()->json([
            'message' => 'Tarefa criada com sucesso!',
            'data' => new TaskResource($task),
        ], 201);
    }

    /**
     * Exibe uma tarefa específica
     */
    public function show(Task $task): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Verificar se o usuário tem acesso à tarefa
        if ($task->created_by !== $user->id 
            && $task->assigned_to !== $user->id 
            && $task->project->user_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para visualizar esta tarefa.',
            ], 403);
        }

        $task->load(['project', 'assignedTo', 'createdBy']);

        return response()->json([
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Atualiza uma tarefa
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        $task->load(['project', 'assignedTo', 'createdBy']);

        return response()->json([
            'message' => 'Tarefa atualizada com sucesso!',
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Remove uma tarefa (soft delete)
     */
    public function destroy(Task $task): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Apenas criador ou dono do projeto pode deletar
        if ($task->created_by !== $user->id && $task->project->user_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para deletar esta tarefa.',
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Tarefa deletada com sucesso!',
        ]);
    }
}