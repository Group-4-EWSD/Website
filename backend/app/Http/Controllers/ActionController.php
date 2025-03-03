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

    /**
     * @OA\Get(
     *     path="/api/articles/{articleId}",
     *     summary="Get article data",
     *     tags={"Articles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="articleId",
     *         in="path",
     *         required=true,
     *         description="ID of the article",
     *         @OA\Schema(type="string", format="uuid", example="6d7b4943-854c-4cb0-a0e3-97022110f228")
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Article not found")
     * )
     */
    public function articlePageInitial($articleId){
        $articlePageData = $this->actionService->getArticleData($articleId);
        return response()->json($articlePageData);
    }
    /**
     * @OA\Post(
     *     path="/like",
     *     summary="Like an article",
     *     tags={"Articles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"articleId"},
     *             @OA\Property(property="articleId", type="string", format="uuid", example="6d7b4943-854c-4cb0-a0e3-97022110f228")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Article liked successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function articleLike(Request $request){
        $reactionCount = $this->actionService->likeArticle($request);
        $this->notificationService->setNotification('1', $request->articleId);
        return response()->json($reactionCount);
    }

    /**
     * @OA\Post(
     *     path="/comment",
     *     summary="Comment on an article",
     *     tags={"Articles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"articleId", "message"},
     *             @OA\Property(property="articleId", type="string", format="uuid", example="6d7b4943-854c-4cb0-a0e3-97022110f228"),
     *             @OA\Property(property="message", type="string", example="This is a great article!")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Comment added successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
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
        return response()->json(['message' => 'Comment added successfully']);
    }

    public function academicYearList(){
        $academicYearList = $this->actionService->academicYearList();
        return response()->json($academicYearList);
    }
}
