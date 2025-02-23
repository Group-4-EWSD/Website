<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Articles",
 *     description="API Endpoints for Articles"
 * )
 */
class ArticleController extends Controller
{

    protected $articleService;
    protected $fileService;

    public function __construct(ArticleService $articleService, FileService $fileService)
    {
        $this->articleService = $articleService;
        $this->fileService = $fileService;
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

    public function createArticle(Request $request)
    {
        $userId = Auth::id();
        $result = $this->articleService->createArticle($userId, $request);

        if ($result['success']) {
            return response()->json(['message' => 'Article created successfully'], 201);
        } else {
            return response()->json(['error' => $result['message']], 500);
        }
    }
}
