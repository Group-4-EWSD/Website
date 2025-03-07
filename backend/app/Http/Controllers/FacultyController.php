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

    public function FacultyDropdown(): JsonResponse
    {
        $facultyDropdown = $this->facultyService->facultyDropdown();
        return response()->json($facultyDropdown, 200);
    }
}
