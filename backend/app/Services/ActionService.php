<?php

namespace App\Services;

use App\Repositories\ActionRepository;
use Aws\S3\S3Client;

class ActionService
{
    protected $actionRepository;
    protected $notificationService;
    protected $s3Client;

    public function __construct(ActionRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;
    }

    public function getArticleData($articleId)
    {
        if (count($this->actionRepository->getUserArticlePair($articleId)) == 0) {
            $this->actionRepository->increaseViewCount($articleId);
        }

        $articleFiles = $this->actionRepository->getArticleDetailFiles($articleId);
        $fileService = app(FileService::class);
        $contentList = [];
        $articlePhotos = [];
        foreach ($articleFiles as $file) {
            $fullFileName = $file->file_name; // Combine name and type
            if ($file->file_type === 'docx') {
                $docPath = $file->file_path;
                $contentList[$fullFileName] = $fileService->readFileContents($docPath);
            } elseif (in_array($file->file_type, ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
                $photoPath = $file->file_path;
                $articlePhotos[$fullFileName] = $photoPath ? "https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/{$photoPath}" : null;
            }
        }

        return [
            'articleDetail' => $this->actionRepository->getArticleDetail($articleId),
            'articleContent' => $contentList,
            'articlePhotos' => $articlePhotos,
            'commentList' => $this->actionRepository->getCommentList($articleId),
            'feedbackList' => $this->actionRepository->getFeedbackList($articleId)
        ];
    }

    public function likeArticle($request){
        $currentReaction = $this->actionRepository->currentReaction($request);
        $currentReaction = $currentReaction == 0 ? 1 : 0;
        return $this->actionRepository->makeAction($currentReaction, $request);
    }

    public function commentArticle($request){
        return $this->actionRepository->createComment($request);
    }

    public function commentDeleteArticle($request){
        return $this->actionRepository->deleteComment($request);
    }

    public function getNotificationList($userId){
        return $this->actionRepository->getNotificationList($userId);
    }

    public function setNotificationView($request){
        return $this->actionRepository->setNotificationView($request);
    }
}
