<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('article_types')->insert([
            ['article_type_id' => Str::uuid(), 'article_type_name' => 'Journal', 'remark' => 'Academic Journal', 'delete_flag' => false, 'created_at' => now(), 'updated_at' => now()],
            ['article_type_id' => Str::uuid(), 'article_type_name' => 'Conference Paper', 'remark' => 'Presented in Conference', 'delete_flag' => false, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
