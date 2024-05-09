<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $teamId = 2;
        return [
            'team_id' => $teamId,
            'user_id' => $user->id,
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),

        ];
    }
}
