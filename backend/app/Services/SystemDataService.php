<?php

namespace App\Services;

use App\Repositories\SystemDataRepository; 
use Illuminate\Support\Str;

class SystemDataService
{
    protected $systemDataRepository;

    public function __construct(SystemDataRepository $systemDataRepository) 
    {
        $this->systemDataRepository = $systemDataRepository;
    }

    public function listAllSysData()
    {
        return $this->systemDataRepository->listAllSysData();
    }

    public function byidSysData($SysId)
    {
        return $this->systemDataRepository->byidSysData($SysId);
    }

    public function byFacSysData($faculty)
    {
        return $this->systemDataRepository->byFacSysData($faculty);
    }

    public function createSysData(array $data)
    {
        $exists = $this->systemDataRepository->checkFacultyAcademicYearCombination(
            $data['faculty_id'],
            $data['academic_year_id']
        );

        if ($exists) {
            return false; 
        }

        $data['system_id'] = Str::uuid()->toString();
        $data['created_at'] = now();
        $data['updated_at'] = now();

        return $this->systemDataRepository->createSysData($data);
    }

    public function updateSysData(array $data)
    {
        $systemId = $data['system_id'];

        $existingData = $this->systemDataRepository->getById($systemId);
        if (!$existingData) {
            return 'not_found';
        }

        if ($existingData->updated_at != $data['updated_at']) {
            return 'timestamp_mismatch';
        }

        $facultyId = $data['faculty_id'] ?? $existingData->faculty_id;
        $academicYearId = $data['academic_year_id'] ?? $existingData->academic_year_id;

        if ($facultyId != $existingData->faculty_id || $academicYearId != $existingData->academic_year_id) {
            $exists = $this->systemDataRepository->checkFacultyAcademicYearCombination(
                $facultyId,
                $academicYearId,
                $systemId
            );
            if ($exists) {
                return 'faculty_academic_conflict';
            }
        }

        // Merge existing data with new data
        $updateData = array_merge((array) $existingData, $data);
        $updateData['updated_at'] = now(); // Set new updated_at
        unset($updateData['system_id']); // Prevent system_id from being updated

        return $this->systemDataRepository->updateSysData($systemId, $updateData);
    }
}