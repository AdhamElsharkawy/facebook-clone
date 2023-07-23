<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */

class UserFactory extends Factory
{
    private $department_index = 0;
    private $role = 'team_leader';
    
    protected $model = User::class;

    public function definition()
    {
        $this->department_index++;
        if($this->department_index > 3) {
            $this->department_index = 1;
            $this->role = 'user';
        }
        return [
            'title' => $this->faker->title(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'image' => 'assets/images/user.png',
            'birth_date' => $this->faker->date(),
            'mobile' => $this->faker->phoneNumber(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'role' => $this->role,
            'department_id' => $this->department_index,
        ];
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
