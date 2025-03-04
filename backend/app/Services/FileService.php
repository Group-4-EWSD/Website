<?php

namespace App\Services;

use App\Repositories\FileRepository;
use Illuminate\Http\UploadedFile;

class FileService
{
    protected $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function uploadFile(UploadedFile $file)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $s3Path = 'documents/' . $fileName;

        $this->fileRepository->uploadToS3($s3Path, $file->path());

        return [
            'message' => 'Uploaded',
            'file_name' => $fileName
        ];
    }

    public function listFiles()
    {
        return $this->fileRepository->listS3Files();
    }

    public function downloadAsZip($fileName)
    {
        $s3Path = 'documents/' . $fileName;
        return $this->fileRepository->downloadAsZip($s3Path, $fileName);
    }

    public function readFileContents($fileName)
    {
        $s3Path = 'documents/' . $fileName;
        return $this->fileRepository->readFileContents($s3Path);
    }
}