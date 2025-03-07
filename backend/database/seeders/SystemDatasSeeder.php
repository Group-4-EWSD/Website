<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemDatasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('system_datas')->insert([
            [
                'system_id' => Str::uuid(),
                'system_title' => 'Research Management',
                'pre_submission_date' => now(),
                'system_status' => 1,
                'creator_id' => 'U0001',
                'category_id' => 'C001',
                'academic_year_id' => DB::table('academic_years')->first()->academic_year_id,
                'delete_flag' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
