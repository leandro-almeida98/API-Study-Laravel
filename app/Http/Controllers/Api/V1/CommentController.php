<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    /**
     * Lista comentários de uma tarefa
     */
    public function index(Request $request, Task $task): AnonymousResourceCollection
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Verificar se o usuário tem acesso à tarefa
        if ($task->created_by !== $user->id 
            && $task->assigned_to !== $user->id 
            && $task->project->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para visualizar os comentários desta tarefa.');
        }

        $comments = $task->comments()
            ->with('user')
            ->latest()
            ->paginate(20);

        return CommentResource::collection($comments);
    }

    /**
     * Cria um novo comentário
     */
    public function store(StoreCommentRequest $request, Task $task): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Verificar se o usuário tem acesso à tarefa
        if ($task->created_by !== $user->id 
            && $task->assigned_to !== $user->id 
            && $task->project->user_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para comentar nesta tarefa.',
            ], 403);
        }

        $comment = Comment::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        $comment->load('user', 'task');

        return response()->json([
            'message' => 'Comentário adicionado com sucesso!',
            'data' => new CommentResource($comment),
        ], 201);
    }

    /**
     * Exibe um comentário específico
     */
    public function show(Comment $comment): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $task = $comment->task;

        // Verificar se o usuário tem acesso à tarefa
        if ($task->created_by !== $user->id 
            && $task->assigned_to !== $user->id 
            && $task->project->user_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para visualizar este comentário.',
            ], 403);
        }

        $comment->load('user', 'task');

        return response()->json([
            'data' => new CommentResource($comment),
        ]);
    }

    /**
     * Atualiza um comentário
     */
    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $comment->update([
            'content' => $request->content,
        ]);

        $comment->load('user', 'task');

        return response()->json([
            'message' => 'Comentário atualizado com sucesso!',
            'data' => new CommentResource($comment),
        ]);
    }

    /**
     * Remove um comentário (soft delete)
     */
    public function destroy(Comment $comment): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Apenas autor ou dono do projeto pode deletar
        if ($comment->user_id !== $user->id && $comment->task->project->user_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para deletar este comentário.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comentário deletado com sucesso!',
        ]);
    }
}
