<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DepartmentSeeder::class,
            UserSeeder::class,
            SeoSeeder::class,
            CollegeSeeder::class,
            CertificationSeeder::class,
            CompanySeeder::class,
            EducationSeeder::class,
            ExperienceSeeder::class,
            EventSeeder::class,
            PostSeeder::class,
            NotificationSeeder::class,
            MentionSeeder::class,
            LikeSeeder::class,
            CommentSeeder::class,
            PollSeeder::class,
        ]);
    }
}
