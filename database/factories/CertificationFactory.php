<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certification>
 */
class CertificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "major" => $this->faker->sentence(3),
            "start_date" => $this->faker->dateTime(),
            "end_date" => $this->faker->dateTime(),
            "is_current" => $this->faker->boolean(),
            "location" => $this->faker->sentence(3),
            "valid_until" => $this->faker->dateTime(),
            "confirmation_link" => $this->faker->sentence(3),
            "college_id" => $this->faker->numberBetween(1, 5),
            "user_id" => $this->faker->numberBetween(1, 20),
        ];
    }
}
