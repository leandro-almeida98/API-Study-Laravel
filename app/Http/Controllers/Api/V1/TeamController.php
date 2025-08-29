<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TeamRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamController extends Controller
{
    /**
     * Lista equipes do usuário
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $teams = Team::with(['owner', 'members', 'projects'])
            ->where(function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                      ->orWhereHas('members', function ($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            })
            ->latest()
            ->paginate(15);

        return TeamResource::collection($teams);
    }

    /**
     * Cria uma nova equipe
     */
    public function store(StoreTeamRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $team = Team::create([
            ...$request->validated(),
            'owner_id' => $user->id,
        ]);

        // Adicionar o criador como owner
        $team->teamMembers()->create([
            'user_id' => $user->id,
            'role' => TeamRole::OWNER->value,
        ]);

        $team->load(['owner', 'members', 'projects']);

        return response()->json([
            'message' => 'Equipe criada com sucesso!',
            'data' => new TeamResource($team),
        ], 201);
    }

    /**
     * Exibe uma equipe específica
     */
    public function show(Team $team): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Verificar se o usuário é membro
        if (!$team->hasMember($user->id) && !$team->isOwner($user->id)) {
            return response()->json([
                'message' => 'Você não tem permissão para visualizar esta equipe.',
            ], 403);
        }

        $team->load(['owner', 'members', 'projects']);

        return response()->json([
            'data' => new TeamResource($team),
        ]);
    }

    /**
     * Atualiza uma equipe
     */
    public function update(UpdateTeamRequest $request, Team $team): JsonResponse
    {
        $team->update($request->validated());
        $team->load(['owner', 'members', 'projects']);

        return response()->json([
            'message' => 'Equipe atualizada com sucesso!',
            'data' => new TeamResource($team),
        ]);
    }

    /**
     * Remove uma equipe
     */
    public function destroy(Team $team): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Apenas owner pode deletar
        if (!$team->isOwner($user->id)) {
            return response()->json([
                'message' => 'Apenas o proprietário pode deletar a equipe.',
            ], 403);
        }

        $team->delete();

        return response()->json([
            'message' => 'Equipe deletada com sucesso!',
        ]);
    }
}
