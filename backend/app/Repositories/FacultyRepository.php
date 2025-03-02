<?php

namespace App\Repositories;

use App\Models\Faculty;

class FacultyRepository
{
    public function getFacultyDropdown()
    {
        return Faculty::select('facultyId','facultyName');
    }
}