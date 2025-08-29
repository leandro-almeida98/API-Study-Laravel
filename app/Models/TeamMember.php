<?php

namespace App\Models;

use App\Enums\TeamRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'role' => TeamRole::class,
        'joined_at' => 'datetime',
    ];

    /**
     * Relacionamento: Membro pertence a uma equipe
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relacionamento: Membro é um usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se pode gerenciar equipe
     */
    public function canManageTeam(): bool
    {
        return $this->role->canManageTeam();
    }

    /**
     * Verifica se pode gerenciar membros
     */
    public function canManageMembers(): bool
    {
        return $this->role->canManageMembers();
    }

    /**
     * Verifica se pode gerenciar projetos
     */
    public function canManageProjects(): bool
    {
        return $this->role->canManageProjects();
    }

    /**
     * Verifica se pode gerenciar tarefas
     */
    public function canManageTasks(): bool
    {
        return $this->role->canManageTasks();
    }
}
