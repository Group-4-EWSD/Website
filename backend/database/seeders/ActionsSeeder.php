<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('actions')->insert([
            [
                'action_id' => Str::uuid(),
                'article_id' => DB::table('articles')->first()->article_id,
                'user_id' => 'U002',
                'react' => 1
            ]
        ]);
    }
}
