<?php

namespace App\Http\Controllers;

use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function getAllAcademicYears()
    {
        $academicYears = $this->academicYearService->getAllAcademicYears();
        return response()->json($academicYears);
    }

    public function getAcademicYearById($academicYearId)
    {
        $academicYear = $this->academicYearService->getAcademicYearById($academicYearId);

        if (!$academicYear) {
            return response()->json(['message' => 'Academic Year not found'], 404);
        }

        return response()->json($academicYear);
    }

    public function createAcademicYear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_start' => 'required|integer|digits:4',
            'academic_year_end' => 'required|integer|digits:4|gt:academic_year_start',
        ]);

        if ($request->academic_year_end - $request->academic_year_start !== 1) {
            return response()->json(['error' => 'academic_year_end must be exactly 1 year after academic_year_start'], 400);
        }

        return $academicYearId = $this->academicYearService->createAcademicYear($request->only([
            'academic_year_start', 'academic_year_end'
        ]));
    }

    public function updateAcademicYear(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'academic_year_id' => 'required|uuid|exists:academic_years,academic_year_id',
                'academic_year_start' => 'required|integer',
                'academic_year_end' => 'required|integer|gt:academic_year_start',
                'updated_at' => 'required|date_format:Y-m-d H:i:s',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
        
            $data = $request->only(['academic_year_start', 'academic_year_end', 'updated_at']);
            $result = $this->academicYearService->updateAcademicYear($request['academic_year_id'], $data);
        
            if ($result === false) {
                return response()->json(['message' => 'Update failed. The record has been modified by another process.'], 409);
            }
        
            return response()->json(['message' => 'Academic Year updated successfully', 'data'=>$result], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
