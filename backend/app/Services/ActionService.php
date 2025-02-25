<?php

namespace App\Services;

use App\Repositories\ActionRepository;
use Aws\S3\S3Client;

class ActionService
{
    protected $actionRepository;
    protected $s3Client;

    public function __construct(ActionRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;
    }

    public function getArticleData($articleId){
        if(count($this->actionRepository->getUserArticlePair($articleId)) == 0){
            $this->actionRepository->increaseViewCount($articleId);
        }
        
        $articleFiles = $this->actionRepository->getArticleDetailFiles($articleId);
        $fileService = app(FileService::class);
        $contentList = [];
        $articlePhotos = [];
        foreach ($articleFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'docx') {
                $contentList[$file] = $fileService->readFileContents($file);
            } elseif ($file->file_type == 'PHOTO') {
                $articlePhotos[] = $file->file_path;
            }
        }
        dd($contentList);

        return [
            'articleDetail' => $this->actionRepository->getArticleDetail($articleId),
            'articleContent' => $articleContent,
            'articlePhotos' => $articlePhotos,
            'commentList' => $this->actionRepository->getCommentList($articleId)
        ];
    }

    public function likeArticle($request){
        $this->actionRepository->makeAction($request);
    }

    public function commentArticle($request){
        $this->actionRepository->createComment($request);
    }
}
