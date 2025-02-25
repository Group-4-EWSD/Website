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

    public function getHomePageData($request)
    {
        return [
            'countData' => $this->articleRepository->getCountData(),
            'allArticles' => $this->articleRepository->getAllArticles($request)
        ];
    }

    public function createArticle($userId, $request)
    {
        DB::beginTransaction();

        try {
            // Generate unique IDs
            $articleId = Str::random(10);

            // Save article
            $this->articleRepository->createArticle($articleId, $userId, $request);

            // Process and save article details (file uploads)
            if ($request->has('article_details')) {
                foreach ($request->article_details as $key => $detail) {
                    if ($request->hasFile("article_details.$key.file")) {
                        $file = $request->file("article_details.$key.file");
                        $fileName = time() . '_' . $file->getClientOriginalName();
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
            DB::commit();
            return ['success' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function draftArticleList(){
        $this->articleRepository->draftArticleList();
    }
}
