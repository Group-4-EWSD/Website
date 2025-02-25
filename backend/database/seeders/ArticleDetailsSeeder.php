<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('article_details')->insert([
            [
                'article_detail_id' => Str::uuid(),
                'article_id' => DB::table('articles')->first()->article_id,
                'file_path' => 'uploads/articles/ai_healthcare.pdf',
                'file_name' => 'AI Healthcare.pdf',
                'file_type' => 'PDF'
            ]
        ]);
    }
}
