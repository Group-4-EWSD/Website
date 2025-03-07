<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch user types and faculties from DB
        $userTypes = DB::table('user_types')->pluck('user_type_id', 'user_type_name');
        $faculties = DB::table('faculties')->pluck('faculty_id', 'faculty_name');

        $users = [
            ['id' => 'U0001', 'user_name' => 'Alice Johnson', 'nickname' => 'Alice', 'user_email' => 'alice@example.com', 'user_password' => Hash::make('password123'), 'user_type_id' => $userTypes['Admin'], 'faculty_id' => $faculties['Computer Science'], 'gender' => 1, 'user_photo_path' => null],
            ['id' => 'U0002', 'user_name' => 'Bob Smith', 'nickname' => 'Bobby', 'user_email' => 'bob@example.com', 'user_password' => Hash::make('password123'), 'user_type_id' => $userTypes['Marketing Manager'], 'faculty_id' => $faculties['Engineering'], 'gender' => 1, 'user_photo_path' => null],
            ['id' => 'U0003', 'user_name' => 'Charlie Davis', 'nickname' => 'Charlie', 'user_email' => 'charlie@example.com', 'user_password' => Hash::make('password123'), 'user_type_id' => $userTypes['Marketing Coordinator'], 'faculty_id' => $faculties['Mathematics'], 'gender' => 1, 'user_photo_path' => null],
            ['id' => 'U0004', 'user_name' => 'David Brown', 'nickname' => 'Dave', 'user_email' => 'david@example.com', 'user_password' => Hash::make('password123'), 'user_type_id' => $userTypes['Student'], 'faculty_id' => $faculties['Art'], 'gender' => 1, 'user_photo_path' => null],
            ['id' => 'U0005', 'user_name' => 'Emma Wilson', 'nickname' => 'Emma', 'user_email' => 'emma@example.com', 'user_password' => Hash::make('password123'), 'user_type_id' => $userTypes['Guest'], 'faculty_id' => $faculties['English'], 'gender' => 2, 'user_photo_path' => null],
            ['id' => 'U0006', 'user_name' => 'Frank White', 'nickname' => 'Franky', 'user_email' => 'frank@example.com', 'user_password' => Hash::make('password123'), 'user_type_id' => $userTypes['Admin'], 'faculty_id' => $faculties['Computer Science'], 'gender' => 1, 'user_photo_path' => null],
            ['id' => 'U0007', 'user_name' => 'Grace Adams', 'nickname' => 'Gracie', 'user_email' => 'grace@example.com', 'user_password' => Hash::make('password123'), 'user_type_id' => $userTypes['Marketing Manager'], 'faculty_id' => $faculties['Mathematics'], 'gender' => 2, 'user_photo_path' => null],
            ['id' => 'U0008', 'user_name' => 'Henry Carter', 'nickname' => 'Henry', 'user_email' => 'henry@example.com', 'user_password' => Hash::make('password123'), 'user_type_id' => $userTypes['Marketing Coordinator'], 'faculty_id' => $faculties['Engineering'], 'gender' => 1, 'user_photo_path' => null],
            ['id' => 'U0009', 'user_name' => 'Isabel Thomas', 'nickname' => 'Izzy', 'user_email' => 'isabel@example.com', 'user_password' => Hash::make('password123'), 'user_type_id' => $userTypes['Student'], 'faculty_id' => $faculties['Art'], 'gender' => 2, 'user_photo_path' => null],
            ['id' => 'U0010', 'user_name' => 'Jack Martin', 'nickname' => 'Jacky', 'user_email' => 'jack@example.com', 'user_password' => Hash::make('password123'), 'user_type_id' => $userTypes['Guest'], 'faculty_id' => $faculties['English'], 'gender' => 1, 'user_photo_path' => null],
        ];

        DB::table('users')->insert($users);
    }
}
