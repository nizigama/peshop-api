<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JWT_Token>
 */
class JWT_TokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "unique_id" => fake()->unique()->uuid,
            "user_id" => User::factory()->create()->id,
            "token_title" => fake()->title,
            "restrictions" => fake()->text,
            "permissions" => fake()->text,
            "expires_at" => fake()->dateTime,
            "last_used_at" => fake()->randomElement([null, fake()->dateTime]),
            "refreshed_at" => fake()->randomElement([null, fake()->dateTime]),
        ];
    }
}
