<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'role' => $this->role->value,
            'role_label' => $this->role->label(),
            'permissions' => $this->role->permissions(),
            'joined_at' => $this->joined_at ? $this->joined_at->toISOString() : now()->toISOString(), // ← Corrigir
            'can_manage_team' => $this->canManageTeam(),
            'can_manage_members' => $this->canManageMembers(),
            'can_manage_projects' => $this->canManageProjects(),
            'can_manage_tasks' => $this->canManageTasks(),
        ];
    }
}
