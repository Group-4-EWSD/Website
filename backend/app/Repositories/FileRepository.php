<?php

namespace App\Repositories;

use Aws\S3\S3Client;
use ZipArchive;

class FileRepository
{
    protected $s3Client;

    public function __construct()
    {
        $this->s3Client = new S3Client([
            'region' => 'ap-southeast-1',
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'verify' => false,
            'http' => ['verify' => false]
        ]);
    }

    public function uploadToS3($s3Path, $filePath)
    {
        $this->s3Client->putObject([
            'Bucket' => 'ewsdcloud',
            'Key' => $s3Path,
            'Body' => file_get_contents($filePath),
        ]);
    }

    public function listS3Files()
    {
        $result = $this->s3Client->listObjectsV2([
            'Bucket' => 'ewsdcloud',
            'Prefix' => 'documents/',
        ]);

        $files = [];
        if (isset($result['Contents'])) {
            foreach ($result['Contents'] as $object) {
                $files[] = $object['Key'];
            }
        }
        return $files;
    }

    public function downloadAsZip($s3Path, $fileName)
    {
        $result = $this->s3Client->getObject([
            'Bucket' => 'ewsdcloud',
            'Key' => $s3Path,
        ]);

        $fileContent = $result['Body']->getContents();
        $zipFileName = time() . '_download.zip';
        $zipPath = storage_path('app/' . $zipFileName);
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new \Exception('Could not create ZIP');
        }

        $zip->addFromString($fileName, $fileContent);
        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}