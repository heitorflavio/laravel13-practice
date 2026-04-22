<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name'    => fake()->words(3, true),
            'type'    => 'exam',
        ];
    }

    public function withResume(): static
    {
        return $this->state(['ia_resume' => fake()->paragraph()]);
    }
}
