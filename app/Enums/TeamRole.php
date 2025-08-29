<?php

namespace App\Enums;

enum TeamRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match($this) {
            self::OWNER => 'Proprietário',
            self::ADMIN => 'Administrador',
            self::MEMBER => 'Membro',
            self::VIEWER => 'Visualizador',
        };
    }

    public function permissions(): array
    {
        return match($this) {
            self::OWNER => [
                'manage_team',
                'delete_team',
                'manage_members',
                'manage_projects',
                'manage_tasks',
                'view_all',
            ],
            self::ADMIN => [
                'manage_members',
                'manage_projects',
                'manage_tasks',
                'view_all',
            ],
            self::MEMBER => [
                'manage_tasks',
                'view_all',
            ],
            self::VIEWER => [
                'view_all',
            ],
        };
    }

    public function canManageTeam(): bool
    {
        return $this === self::OWNER;
    }

    public function canManageMembers(): bool
    {
        return in_array($this, [self::OWNER, self::ADMIN]);
    }

    public function canManageProjects(): bool
    {
        return in_array($this, [self::OWNER, self::ADMIN]);
    }

    public function canManageTasks(): bool
    {
        return in_array($this, [self::OWNER, self::ADMIN, self::MEMBER]);
    }
}