<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => rand(1, 10),
            'user_id' => rand(1, 10),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'start_date' => $this->faker->dateTimeBetween('-10 years', '-5 years'),
            'end_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
        ];
    }
}
