<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = [
            ['faculty_id' => Str::uuid(), 'faculty_name' => 'Computer Science', 'remark' => null],
            ['faculty_id' => Str::uuid(), 'faculty_name' => 'Engineering', 'remark' => null],
            ['faculty_id' => Str::uuid(), 'faculty_name' => 'Mathematics', 'remark' => null],
            ['faculty_id' => Str::uuid(), 'faculty_name' => 'Art', 'remark' => null],
            ['faculty_id' => Str::uuid(), 'faculty_name' => 'English', 'remark' => null],
        ];

        DB::table('faculties')->insert($faculties);
    }
}
