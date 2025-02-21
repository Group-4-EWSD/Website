<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileService;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function upload(Request $request)    
    {
        $file = $request->file('file');
        if (!$file) {
            return response()->json(['error' => 'No file'], 400);
        }

        $result = $this->fileService->uploadFile($file);
        return response()->json($result, 200);
    }

    public function listFiles()
    {
        $files = $this->fileService->listFiles();
        return response()->json([
            'message' => 'Listed',
            'files' => $files
        ], 200);
    }

    public function downloadAsZip($fileName)
    {
        return $this->fileService->downloadAsZip($fileName);
    }
}