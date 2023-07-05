<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 */
class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "reaction" => $this->faker->numberBetween(1, 3),
            "post_id" => $this->faker->numberBetween(1, 80),
            "user_id" => $this->faker->numberBetween(1, 20),
        ];
    }
}
