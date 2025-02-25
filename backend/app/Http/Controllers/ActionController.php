<?php

namespace App\Http\Controllers;

use App\Services\ActionService;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    
    protected $actionService;

    public function __construct(ActionService $actionService)
    {
        $this->actionService = $actionService;
    }

    public function articlePageInitial($articleId){
        $articlePageData = $this->actionService->getArticleData($articleId);
        return response()->json($articlePageData);
    }
    public function articleLike(Request $request){
        $this->actionService->likeArticle($request);
    }
    public function articleComment(Request $request){
        $this->actionService->commentArticle($request);
    }
}
