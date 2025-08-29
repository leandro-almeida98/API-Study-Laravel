<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddTeamMemberRequest;
use App\Http\Resources\TeamMemberResource;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamMemberController extends Controller
{
    public function index(Team $team): AnonymousResourceCollection
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$team->hasMember($user->id) && !$team->isOwner($user->id)) {
            abort(403, 'Você não tem permissão para visualizar os membros desta equipe.');
        }

        $members = $team->teamMembers()->with('user')->get();

        return TeamMemberResource::collection($members);
    }

    public function store(AddTeamMemberRequest $request, Team $team): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $currentMember = $team->teamMembers()->where('user_id', $user->id)->first();
        
        if (!$team->isOwner($user->id) && (!$currentMember || !$currentMember->canManageMembers())) {
            return response()->json([
                'message' => 'Você não tem permissão para adicionar membros.',
            ], 403);
        }

        $member = $team->teamMembers()->create([
            'user_id' => $request->user_id,
            'role' => $request->role,
        ]);

        $member->load('user');

        return response()->json([
            'message' => 'Membro adicionado com sucesso!',
            'data' => new TeamMemberResource($member),
        ], 201);
    }

    public function update(Request $request, TeamMember $member): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $team = $member->team;
        $currentMember = $team->teamMembers()->where('user_id', $user->id)->first();
        
        if (!$team->isOwner($user->id) && (!$currentMember || !$currentMember->canManageMembers())) {
            return response()->json([
                'message' => 'Você não tem permissão para atualizar membros.',
            ], 403);
        }

        if ($member->user_id === $user->id) {
            return response()->json([
                'message' => 'Você não pode alterar sua própria função.',
            ], 403);
        }

        $request->validate([
            'role' => ['required', 'in:owner,admin,member,viewer'],
        ]);

        $member->update([
            'role' => $request->role,
        ]);

        $member->load('user');

        return response()->json([
            'message' => 'Função atualizada com sucesso!',
            'data' => new TeamMemberResource($member),
        ]);
    }

    public function destroy(TeamMember $member): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $team = $member->team;
        $currentMember = $team->teamMembers()->where('user_id', $user->id)->first();
        
        if (!$team->isOwner($user->id) && (!$currentMember || !$currentMember->canManageMembers())) {
            return response()->json([
                'message' => 'Você não tem permissão para remover membros.',
            ], 403);
        }

        if ($member->user_id === $team->owner_id) {
            return response()->json([
                'message' => 'Não é possível remover o proprietário da equipe.',
            ], 403);
        }

        $member->delete();

        return response()->json([
            'message' => 'Membro removido com sucesso!',
        ]);
    }
}
