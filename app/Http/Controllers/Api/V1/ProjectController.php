<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    /**
     * Lista todos os projetos do usuário autenticado
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $projects = Project::with('user')
            ->byUser($user->id)
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15);

        return ProjectResource::collection($projects);
    }

    /**
     * Cria um novo projeto
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $project = Project::create([
            ...$request->validated(),
            'user_id' => $user->id,
        ]);

        $project->load('user');

        return response()->json([
            'message' => 'Projeto criado com sucesso!',
            'data' => new ProjectResource($project),
        ], 201);
    }

    /**
     * Exibe um projeto específico
     */
    public function show(Project $project): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Verificar se o usuário é o dono do projeto
        if ($project->user_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para visualizar este projeto.',
            ], 403);
        }

        $project->load('user');

        return response()->json([
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Atualiza um projeto
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());
        $project->load('user');

        return response()->json([
            'message' => 'Projeto atualizado com sucesso!',
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Remove um projeto (soft delete)
     */
    public function destroy(Project $project): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Verificar se o usuário é o dono do projeto
        if ($project->user_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para deletar este projeto.',
            ], 403);
        }

        $project->delete();

        return response()->json([
            'message' => 'Projeto deletado com sucesso!',
        ]);
    }
}