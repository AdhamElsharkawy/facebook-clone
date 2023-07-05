<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Education>
 */
class EducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'degree' => $this->faker->sentence(3),
            "major" => $this->faker->sentence(3),
            'start_date' => $this->faker->dateTime(),
            'end_date' => $this->faker->dateTime(),
            'college_id' => $this->faker->numberBetween(1, 5),
            'user_id' => $this->faker->numberBetween(1, 20),
        ];
    }
}
