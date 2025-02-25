<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('comments')->insert([
            [
                'comment_id' => Str::uuid(),
                'article_id' => DB::table('articles')->first()->article_id, 
                'user_id' => DB::table('users')->first()->user_id, 
                'message' => 'This is a comment message.',
                'message_type' => 1,
                'delete_flag' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
