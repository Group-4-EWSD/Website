<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('articles')->insert([
            [
                'article_id' => Str::uuid(),
                'article_title' => 'AI in Healthcare',
                'article_description' => 'Research on AI applications in healthcare.',
                'user_id' => DB::table('users')->first()->user_id,
                'system_id' => DB::table('system_datas')->first()->system_id,
                'approver_id' => null,
                'article_type_id' => DB::table('article_types')->first()->article_type_id,
                'delete_flag' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
