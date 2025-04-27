<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ContactUsRepository
{
    public function createContactUs($data){
        DB::table('contact_us')->insert([
            'contact_us_id' => $data['contact_us_id'],
            'visitor_name' => $data['visitor_name'],
            'visitor_email' => $data['visitor_email'],
            'title' => $data['title'],
            'description' => $data['description'],
            'created_at' => now(),
            'updated_at' => now()
        ])
        ->orderBy('created_at', 'desc');

        return $data['contact_us_id'];
    }

    public function getContactUsList(){
        return DB::table('contact_us')->get();
    }
}