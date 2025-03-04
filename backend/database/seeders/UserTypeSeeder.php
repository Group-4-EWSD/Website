<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = [
            ['user_type_id' => Str::uuid(), 'user_type_name' => 'Guest'],
            ['user_type_id' => Str::uuid(), 'user_type_name' => 'Student'],
            ['user_type_id' => Str::uuid(), 'user_type_name' => 'Marketing Coordinator'],
            ['user_type_id' => Str::uuid(), 'user_type_name' => 'Marketing Manager'],
            ['user_type_id' => Str::uuid(), 'user_type_name' => 'Admin'],
        ];

        DB::table('user_types')->insert($userTypes);
    }
}