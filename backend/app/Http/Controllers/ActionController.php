<?php

namespace App\Http\Controllers;

use App\Services\ActionService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    
    protected $actionService;
    protected $notificationService;

    public function __construct(ActionService $actionService, NotificationService $notificationService)
    {
        $this->actionService = $actionService;
        $this->notificationService = $notificationService;
    }
    public function articlePageInitial ()
    {
        dd('ok');
        $articlePageData = $this->actionService->getArticleData($articleId);
        return response()->json($articlePageData);
    }
    public function articleLike(Request $request){
        $reactionCount = $this->actionService->likeArticle($request);
        $this->notificationService->setNotification('1', $request->articleId);
        return response()->json(['likeCount' => $reactionCount]);
    }
    public function articleComment(Request $request) {
        $success = $this->actionService->commentArticle($request);
        $this->notificationService->setNotification('2', $request->articleId);
        if ($success) {
            return response()->json(['message' => 'Comment added successfully'], 201);
        } else {
            return response()->json(['message' => 'Failed to add comment'], 500);
        }
    }

    public function articleCommentDelete(Request $request){
        $this->actionService->commentDeleteArticle($request);
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
