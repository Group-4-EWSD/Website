<?php

namespace App\Services;

use App\Repositories\UserTypeRepository;

class UserTypeService
{
    protected $userTypeRepository;

    public function __construct(UserTypeRepository $userTypeRepository)
    {
        $this->userTypeRepository = $userTypeRepository;
    }
}