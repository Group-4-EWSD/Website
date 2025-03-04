<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            UserTypeSeeder::class,
            FacultySeeder::class,
            AcademicYearSeeder::class,
            SystemDatasSeeder::class,
            UsersSeeder::class,
            ArticleTypesSeeder::class,
            ArticlesSeeder::class,
            ArticleDetailsSeeder::class,
            ActionsSeeder::class,
            ActivitiesSeeder::class,
            FeedbacksSeeder::class,
            LoginHistoriesSeeder::class,
            TermsAndConditionsSeeder::class,
            CommentsSeeder::class
        ]);
    }
}
