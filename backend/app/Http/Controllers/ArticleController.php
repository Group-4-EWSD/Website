<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\Request;
/**
 * @OA\Tag(
 *     name="Articles",
 *     description="API Endpoints for Articles"
 * )
 */
class ArticleController extends Controller
{

    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Home Page Initial",
     *     tags={"Articles"},
     * 
     *     @OA\Parameter(name="articleTitle",in="query",description="Filter articles by title",required=false,@OA\Schema(type="string")),
     *     @OA\Parameter(name="displayNumber",in="query",description="Number of articles per page",required=false,@OA\Schema(type="integer", default=5)),
     *     @OA\Parameter(name="pageNumber",in="query",description="Page number for pagination",required=false,@OA\Schema(type="integer", default=1)),
     * 
     *     @OA\Response(response=200, description="List of Articles"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     * )
     */
    public function homePageInitial(Request $request)
    {
        $homePageData = $this->articleService->getHomePageData($request);
        return response()->json($homePageData);
    }
}
