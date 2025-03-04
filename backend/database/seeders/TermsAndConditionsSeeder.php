<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TermsAndConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('terms_conditions')->insert([
            [
                'term_condition_id' => Str::uuid(),
                'term_condition' => 'These are the terms and conditions of the application.',
            ],
        ]);
    }
}
