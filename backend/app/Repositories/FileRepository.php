<?php

namespace App\Repositories;

use Aws\S3\S3Client;
use PhpOffice\PhpWord\IOFactory;
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

    public function deleteFromS3($s3Path): void
    {
        $this->s3Client->deleteObject([
            'Bucket' => 'ewsdcloud',
            'Key' => $s3Path,
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

    public function readFileContents($s3Path)
    {
        $result = $this->s3Client->getObject([
            'Bucket' => 'ewsdcloud',
            'Key' => $s3Path,
        ]);

        $fileContent = $result['Body']->getContents();

        // Use system temp directory (Vercel compatible)
        $tempPath = sys_get_temp_dir() . '/temp_' . time() . '.docx';

        // Store temp file
        if (file_put_contents($tempPath, $fileContent) === false) {
            throw new \Exception('Failed to write temp file.');
        }

        // Read DOCX contents
        $phpWord = IOFactory::load($tempPath);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    foreach ($element->getElements() as $textElement) {
                        if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                            $text .= $textElement->getText() . ' ';
                        }
                    }
                }
            }
        }

        // Delete temp file
        unlink($tempPath);

        return trim($text);
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

    public function downloadMultipleAsZip($files)
    {
        $zipFileName = time() . '_download.zip';
        $zipPath = sys_get_temp_dir() . '/' . $zipFileName;
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new \Exception('Could not create ZIP');
        }

        foreach ($files as $file) {
            $s3Path = $file->file_path;
            $articleTitle = preg_replace('/[^\w\-]/', '_', $file->article_title); // Sanitize title
            $fileName = basename($file->file_path); // e.g. 'document.pdf'

            $result = $this->s3Client->getObject([
                'Bucket' => 'ewsdcloud',
                'Key' => $s3Path,
            ]);

            $fileContent = $result['Body']->getContents();

            // Add to a folder named after article_title inside the ZIP
            $zip->addFromString("{$articleTitle}/{$fileName}", $fileContent);
        }

        $zip->close(); // Finalize ZIP

        // Define S3 Path (e.g., in "downloads/" folder)
        $s3ZipPath = 'downloads/' . $zipFileName;

        // Upload ZIP to S3 using your function
        $this->uploadToS3($s3ZipPath, $zipPath);

        // Delete local ZIP file after upload
        unlink($zipPath);

        // Generate a temporary signed URL for download (valid for 1 hour)
        $downloadUrl = $this->s3Client->getObjectUrl('ewsdcloud', $s3ZipPath);

        return response()->json(['download_url' => $downloadUrl]); // Open Url and it will download automatically.
        // return redirect()->away($downloadUrl);
    }
}
