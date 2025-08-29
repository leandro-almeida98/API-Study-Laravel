<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Team',
            'description' => fake()->sentence(10),
            'owner_id' => User::factory(),
        ];
    }

    /**
     * Equipe de desenvolvimento
     */
    public function development(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Equipe de Desenvolvimento',
            'description' => 'Time responsável pelo desenvolvimento de software',
        ]);
    }

    /**
     * Equipe de design
     */
    public function design(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Equipe de Design',
            'description' => 'Time responsável pelo design e UX',
        ]);
    }
}
