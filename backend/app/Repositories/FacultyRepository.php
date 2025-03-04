<?php

namespace App\Repositories;

use App\Models\Faculty;

class FacultyRepository
{
    public function getFacultyDropdown()
    {
        return Faculty::select('faculty_id','faculty_name');
    }
}