<?php

namespace App\Services;

use App\Repositories\FacultyRepository;

class FacultyService
{
    protected $facultyRepository;

    public function __construct(FacultyRepository $facultyRepository)
    {
        $this->facultyRepository = $facultyRepository;
    }

    public function facultyDropdown()
    {
        return $this->facultyRepository->getFacultyDropdown();
    }
}