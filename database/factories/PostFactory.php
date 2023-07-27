<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "thread" => $this->faker->sentence(3),
            "images" => json_encode([
                "assets/images/default.png",
                "assets/images/default.png",
                "assets/images/default.png",
            ]),
            "poll_end_date" => $this->faker->dateTimeBetween('+1 day', '+2 day'),
            "poll_caption" => $this->faker->sentence(3),
            "user_id" => $this->faker->numberBetween(1, 20),
        ];
    }
}
