<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\FacultyService;

class FacultyController extends Controller
{
    protected $facultyService;

    public function __construct(FacultyService $facultyService)
    {
        $this->facultyService = $facultyService;
    }

    public function listAllFaculties()
    {
        return $listAllFaculties = $this->facultyService->getAllFaculties();
    }

    public function selectFacultyByID($request)
    {
        return $selectFacultyByID = $this->facultyService->selectFacultyByID($request);
    }

    public function createFaculty(Request $request)
    {
        try {
            $validated = $request->validate([
                'faculty_name' => 'required|string|max:255|unique:faculties,faculty_name',
                'remark'       => 'nullable|string'
            ]);
    
            $result = $this->facultyService->createFaculty($validated);
    
            return response()->json([
                'message'    => 'Faculty created successfully',
                'faculty_id' => $result['faculty_id']
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->validator->errors()->first()
            ], 400);
        }
    }
    
    public function updateFaculty(Request $request)
    {
        try {
            $validated = $request->validate([
                'faculty_id'   => 'required|string|exists:faculties,faculty_id',
                'faculty_name' => 'sometimes|required|string|max:255|unique:faculties,faculty_name,' . $request->faculty_id . ',faculty_id',
                'remark'       => 'nullable|string',
                'updated_at'   => 'required|date'
            ]);

            $result = $this->facultyService->updateFaculty($validated);

            if ($result) {
                return response()->json([
                    'message' => 'Faculty updated successfully'
                ], 200);
            }

            return response()->json([
                'message' => 'Update failed: Data has been modified by another process'
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
