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
        DB::beginTransaction();

        try {
            // Generate unique IDs
            $articleId = Str::uuid();

            // Save article
            $this->articleRepository->createArticle($articleId, $userId, $request);

            // Process and save article details (file uploads)
            if ($request->has('article_details')) {
                foreach ($request->article_details as $key => $detail) {
                    if ($request->hasFile("article_details.$key.file")) {
                        $file = $request->file("article_details.$key.file");
                        // $fileName = time() . '_' . $file->getClientOriginalName();
                        $fileName = time() . '_' . $request->article_title;
                        $s3Path = 'documents/' . $fileName;
                        // Upload file to S3
                        $this->fileRepository->uploadToS3($s3Path, $file->path());
                        // $uploadResult = $this->fileRepository->uploadFile($file);
                        $filePath = 'https://ewsdcloud.s3.amazonaws.com/documents/' . $fileName;

                        // Save article details
                        $this->articleRepository->createArticleDetail($articleId, $filePath, $fileName, $file->getClientOriginalExtension());
                    }
                }
            }
            $systemId = $this->articleRepository->getSystemId($userId, $request->status);
            $this->articleRepository->createActivity($articleId, $userId, $systemId, $request);

            DB::commit();
            return ['success' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function draftArticleList($userId){
        return $this->articleRepository->draftArticleList($userId);
    }

    public function getFileList($articleId){
        $this->articleRepository->getFileList($articleId);
    }
}
