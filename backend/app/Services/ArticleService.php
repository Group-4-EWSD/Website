<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Repositories\FileRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService
{
    protected $articleRepository;
    protected $fileRepository;

    public function __construct(ArticleRepository $articleRepository, FileRepository $fileRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->fileRepository = $fileRepository;
    }

    public function getHomePageData($userId, $request)
    {
        return [
            'countData' => $this->articleRepository->getCountData(),
            'allArticles' => $this->articleRepository->getAllArticles($userId, $request)->get()
        ];
    }

    public function getMyArticles($userId, $facultyId, $request)
    {
        $deadlines = $this->articleRepository->getDeadlines($facultyId);
        $myArticles = $this->articleRepository->getAllArticles($userId, $request);
        $latestArticles = $myArticles->orderBy('created_at', 'desc')->take(3)->get();
        return [
            'preUploadDeadline' => $deadlines->pre_submission_date,
            'actualUploadDeadline' => $deadlines->actual_submission_date,
            'latestArticles' => $latestArticles,
            'myArticles' => $myArticles->get()
        ];
    }

    public function createArticle($userId, $request)
    {
        
        $systemId = $this->articleRepository->getSystemId($userId, $request->status);
        DB::beginTransaction();

        try {
            // Generate unique IDs
            $articleId = Str::uuid();

            // Save article
            $this->articleRepository->createArticle($articleId, $userId, $systemId, $request);
            // Process and save article details (file uploads)
            // dd($request);
            if ($request->hasFile('article_details')) {
                $uploadedFiles = []; // Array to store filenames
            
                foreach ($request->file('article_details') as $key => $file) {
                    if ($file->isValid()) { 
                        // Generate filename
                        $fileName = time() . '_' . $request->article_title . '.' . $file->getClientOriginalExtension();
                        $s3Path = 'documents/' . $fileName;
            
                        // Upload file to S3
                        $this->fileRepository->uploadToS3($s3Path, $file->getRealPath());
            
                        // File URL on S3
                        $filePath = 'https://ewsdcloud.s3.amazonaws.com/documents/' . $fileName;
            
                        // Save the filename used
                        $uploadedFiles[] = $fileName;
            
                        // Save article details
                        $this->articleRepository->createArticleDetail($articleId, $filePath, $fileName, $file->getClientOriginalExtension());
                    }
                }
            
                // Debugging: Show all uploaded filenames
                dd($uploadedFiles);
            }
                       
            $this->articleRepository->createActivity($articleId, $userId, $systemId, $request);

            DB::commit();
            return ['success' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function changeArticleStatus($userId, $articleId, $request){
        return $this->articleRepository->changeArticleStatus($userId, $articleId, $request);
    }

    public function draftArticleList($userId){
        return $this->articleRepository->draftArticleList($userId);
    }

    public function getFileList($articleId){
        $this->articleRepository->getFileList($articleId);
    }
}
