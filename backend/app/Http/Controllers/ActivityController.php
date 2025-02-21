<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Illuminate\Http\Request;

class activityController extends Controller
{
    
    protected $articleService;

    public function __construct(ActivityService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function articlePageInitial(Request $request){
        $articlePageData = $this->articleService->getArticleData($request);
        return response()->json($articlePageData);
    }
    public function articleLike(Request $request){
        $this->articleService->likeArticle($request);
        // return response()->success();
    }
    public function articleComment(Request $request){
        $this->articleService->commentArticle($request);
    }
}
