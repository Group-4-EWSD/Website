<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('activities')->insert([
            [
                'activity_id' => Str::uuid(),
                'article_id' => DB::table('articles')->first()->article_id,
                'user_id' => DB::table('users')->first()->user_id,
                'status' => 1
            ]
        ]);
    }
}
