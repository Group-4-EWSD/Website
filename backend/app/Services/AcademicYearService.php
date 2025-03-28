<?php

namespace App\Services;

use App\Repositories\AcademicYearRepository;

class AcademicYearService
{
    protected $academicYearRepository;

    public function __construct(AcademicYearRepository $academicYearRepository)
    {
        $this->academicYearRepository = $academicYearRepository;
    }

    public function getAllAcademicYears()
    {
        return $this->academicYearRepository->getAllAcademicYears();
    }

    public function getAcademicYearById($academicYearId)
    {
        return $this->academicYearRepository->getAcademicYearById($academicYearId);
    }

    public function createAcademicYear($data)
    {
        $data['academic_year_description'] = $data['academic_year_start'] . '-' . $data['academic_year_end'];

        return $this->academicYearRepository->createAcademicYear($data);
    }

    public function updateAcademicYear($academicYearId, $data)
    {
        $data['academic_year_description'] = $data['academic_year_start'] . '-' . $data['academic_year_end'];

        return $this->academicYearRepository->updateAcademicYear($academicYearId, $data);
    }
}
