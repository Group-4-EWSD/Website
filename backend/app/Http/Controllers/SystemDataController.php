<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemDataService;

class SystemDataController extends Controller
{
    protected $systemDataService;

    public function __construct(SystemDataService $systemDataService) 
    {
        $this->systemDataService = $systemDataService;
    }

    public function listAllSysData()
    {
        return $this->systemDataService->listAllSysData();
    }

    public function byidSysData($SysId)
    {
        return $this->systemDataService->byidSysData($SysId);
    }

    public function byFacSysData($faculty)
    {
        return $this->systemDataService->byFacSysData($faculty);
    }

    public function createSysData(Request $request)
    {
        try {
            $validated = $request->validate([
                'system_title' => 'required|string|max:255',
                'pre_submission_date' => 'required|date',
                'actual_submission_date' => 'required|date|after_or_equal:pre_submission_date',
                'system_status' => 'required|in:0,1',
                'faculty_id' => 'required|uuid|exists:faculties,faculty_id',
                'academic_year_id' => 'required|uuid|exists:academic_years,academic_year_id',
            ]);

            $result = $this->systemDataService->createSysData($validated);

            if ($result === false) {
                return response()->json(['message' => 'This faculty already has a submission date set for this academic year.'], 422);
            }

            return response()->json(['message' => 'System data created successfully', 'SystemData_id' => $result], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateSysData(Request $request)
    {
        try {
            $validated = $request->validate([
                'system_id' => 'required|uuid|exists:system_datas,system_id',
                'system_title' => 'sometimes|string|max:255',
                'pre_submission_date' => 'sometimes|date',
                'actual_submission_date' => 'sometimes|date|after_or_equal:pre_submission_date',
                'system_status' => 'sometimes|in:0,1',
                'faculty_id' => 'sometimes|uuid|exists:faculties,faculty_id',
                'academic_year_id' => 'sometimes|uuid|exists:academic_years,academic_year_id',
                'updated_at' => 'required|date',
            ]);
    
            $result = $this->systemDataService->updateSysData($validated);

            if ($result === 'not_found') {
                return response()->json(['message' => 'System data not found'], 404);
            }

            if ($result === 'timestamp_mismatch') {
                return response()->json(['message' => 'Update failed: Data has been modified by another process'], 409);
            }

            if ($result === 'faculty_academic_conflict') {
                return response()->json(['message' => 'This faculty already has a submission date set for this academic year.'], 422);
            }

            return response()->json(['message' => 'System data updated successfully', 'data' => $result], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}