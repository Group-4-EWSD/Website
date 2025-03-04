<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('academic_years')->insert([
            ['academic_year_id' => Str::uuid(), 'academic_year' => '2024-2025'],
            ['academic_year_id' => Str::uuid(), 'academic_year' => '2025-2026'],
        ]);
    }
}
