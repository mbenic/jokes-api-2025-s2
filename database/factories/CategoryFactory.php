<?php

namespace Database\Factories;

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
        return [
            // "title" => $this->faker->word(),
            // "description" => $this->faker->sentence(),

             "title" => fake()->word(),
            "description" => fake()->sentence(),
            "deleted_at" => fake()->randomDigit() > 8 ? fake()->dateTime() : null // Assuming soft deletes are used
            //
        ];
    }
}
