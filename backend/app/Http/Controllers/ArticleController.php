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
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="articleTitle",
     *         in="query",
     *         description="Filter articles by title",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="displayNumber",
     *         in="query",
     *         description="Number of articles per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=5)
     *     ),
     *     @OA\Parameter(
     *         name="pageNumber",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     * 
     *     @OA\Response(response=200, description="List of Articles"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     * )
     */
    public function homePageInitial(Request $request)
    {
        $userId = Auth::id();
        $homePageData = $this->articleService->getHomePageData($userId, $request);
        return response()->json($homePageData);
    }

    public function myArticleInitial(Request $request)
    {
        $userId = Auth::id();
        $facultyId = Auth::user()->faculty_id;
        $myArticleData = $this->articleService->getMyArticles($userId, $facultyId, $request);
        return response()->json($myArticleData);
    }

    public function articleCreate(Request $request)
    {
        $userId = Auth::id();
        $result = $this->articleService->createArticle($userId, $request);

        if ($result['success']) {
            return response()->json(['message' => 'Article created successfully'], 201);
        } else {
            return response()->json(['error' => $result['message']], 500);
        }
    }
    public function draftArticleList(){
        $articles = $this->articleService->draftArticleList();
        return response()->json($articles);
    }

    public function articleDownload($articleId){
        $articleFileList = $this->articleService->getFileList($articleId);
        if (!empty($articleFileList)) {
            for ($i=0; $i < $articleFileList; $i++) { 
                $this->fileService->downloadAsZip($articleFileList[$i]);
            }
        }
    }
}
