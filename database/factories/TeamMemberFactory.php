<?php

namespace Database\Factories;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'user_id' => User::factory(),
            'role' => fake()->randomElement(['admin', 'member', 'viewer']),
            'joined_at' => now(),
        ];
    }

    /**
     * Membro owner
     */
    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => TeamRole::OWNER->value,
        ]);
    }

    /**
     * Membro admin
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => TeamRole::ADMIN->value,
        ]);
    }

    /**
     * Membro comum
     */
    public function member(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => TeamRole::MEMBER->value,
        ]);
    }

    /**
     * Visualizador
     */
    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => TeamRole::VIEWER->value,
        ]);
    }
}
