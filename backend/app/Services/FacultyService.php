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

    public function getAllFaculties()
    {
        return $listAllFaculties = $this->facultyRepository->getAllFaculties();
    }

    public function selectFacultyByID($data)
    {
        return $selectFacultyByID = $this->facultyRepository->selectFacultyByID($data);
    }

    public function createFaculty($data)
    {
        return $this->facultyRepository->createFaculty($data);
    }

    public function updateFaculty($data)
    {
        return $this->facultyRepository->update($data);
    }
}