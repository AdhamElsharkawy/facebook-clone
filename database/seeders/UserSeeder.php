<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'title'=> 'Sir.',
            'name' => 'super admin',
            'email' => 'super_admin@app.com',
            "birth_date" => "1990-01-01",
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'role' => 'super_admin',
            'department_id' => 1,
        ]);
        User::factory(20)->create();
    }
}
