<?php

namespace App\Repositories;

use App\Models\UserType;

class UserTypeRepository
{
    public function getUserTypeDropdown()
    {
        return UserType::all();
    }
}