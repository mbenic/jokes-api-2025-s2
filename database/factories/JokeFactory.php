<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use \App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Joke>
 */
class JokeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "title" => fake()->word(),
            "content" => fake()->sentence(),
           //"user_id" => '100', // Assuming user IDs are positive integers
            'user_id' => User::factory(), // ensures a user exists
            //"published_at" => fake()->randomDigit() > 8 ? fake()->dateTime() : null, // Randomly set published_at to null or a date
            "created_at" => fake()->dateTime(),
            "updated_at" => fake()->dateTime(),
            // If using soft deletes, you can add a deleted_at field
          // "deleted_at" => fake()->randomDigit() > 8 ? fake()->dateTime() : null // Assuming soft deletes are used
        ];
    }
}
