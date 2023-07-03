<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            SeoSeeder::class,
            CertificationSeeder::class,
            CompanySeeder::class,
            CollegeSeeder::class,
            CommentSeeder::class,
            DepartmentSeeder::class,
            EducationSeeder::class,
            EventSeeder::class,
            ExperienceSeeder::class,
            LikeSeeder::class,
            MentionSeeder::class,
            NotificationSeeder::class,
            PostSeeder::class,
            PollSeeder::class,
        ]);
    }
}
