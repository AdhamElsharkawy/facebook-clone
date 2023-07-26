<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "type" => $this->faker->randomElement(["like", "comment", "mention"]),
            "post_id" => $this->faker->numberBetween(1, 80),
            "user_id" => $this->faker->numberBetween(1, 20),
        ];
    }
}
