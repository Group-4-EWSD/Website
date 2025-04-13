<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedMail;
use App\Services\ArticleService;
use App\Services\FileService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
    protected $notificationService;

    public function __construct(ArticleService $articleService, FileService $fileService, NotificationService $notificationService)
    {
        $this->articleService = $articleService;
        $this->fileService = $fileService;
        $this->notificationService = $notificationService;
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
        $user = Auth::user();
        $userId = $user->id;
        $userType = $user->user_type_id;
        $facultyId = $user->faculty_id;
        if($userType == '0'){ // Guest
            $homePageData = $this->articleService->getGuestHomePageData($userId, $request);
        }else if($userType == '1'){ // Student
            // dd(Auth::id());
            $homePageData = $this->articleService->getStudentHomePageData($userId, $request);
            // $mailSent = Mail::to(Auth::user()->user_email)->send(new UserCreatedMail(Auth::user()));
            // if ($mailSent) {
            // } else {
            //      return response()->json($homePageData);
            //     return response()->json(['message' => 'Failed to send email.'], 201);
            // }
        }else if($userType == '2'){ // Marketing Coordinator
            $homePageData = $this->articleService->getCoordinatorHomePageData($userId, $facultyId, $request);
        }else if($userType == '3'){ // Marketing Manager
            $homePageData = $this->articleService->getManagerHomePageData($userId);
        }else if($userType == '4'){ // Admin
            $homePageData = $this->articleService->getAdminReports();
        }else{
            return response()->json(['message'=> "User Role Missing"], 201);
        }
        return response()->json($homePageData);
    }

    public function myArticleInitial(Request $request)
    {
        $userId = Auth::id();
        $facultyId = Auth::user()->faculty_id;
        $myArticleData = $this->articleService->getMyArticles($userId, $facultyId, $request);
        return response()->json($myArticleData);
    }

    public function coordinatorArticles(Request $request)
    {
        $userId = Auth::id();
        $facultyId = Auth::user()->faculty_id;
        $myArticleData = $this->articleService->getCoordinatorArticles($facultyId, $request);
        return response()->json($myArticleData);
    }

    public function managerArticles(Request $request)
    {
        $articles = $this->articleService->getManagerArticles($request);
        return response()->json($articles);
    }

    public function articleCreateUpdate(Request $request)
    {
        $userId = Auth::id();
        $result = $this->articleService->createUpdateArticle($userId, $request);

        if ($result['success']) {
            if (empty($request->article_id)) {
                $coordinatorEmail = DB::table('users')
                    ->where('faculty_id', Auth::user()->faculty_id)
                    ->where('user_type_id', 2)
                    ->value('user_email');

                $mailSent = Mail::to($coordinatorEmail)->send(new UserCreatedMail(
                    Auth::user(),
                    $request->article_id ?: Str::uuid(), 
                    $request->article_title,
                    Auth::user()->user_name,
                    Auth::user()->nickname,
                    Auth::user()->user_email
                ));

                if ($mailSent) {
                    return response()->json(['message1' => 'Article created successfully.', 'message2' => 'Successfully send email.'], 201);
                } else {
                    return response()->json(['message' => 'Failed to send email.'], 201);
                }
            } else {
                return response()->json(['message' => 'Article updated successfully'], 201);
            }
        } else {
            return response()->json(['error' => $result['message']], 500);
        }
    }

    public function articleChangeStatus($articleId, Request $request)
    {
        $userId = Auth::id();
        $result = $this->articleService->changeArticleStatus($userId, $articleId, $request);
        if ($result) {
            return response()->json(['message' => 'Article status changed successfully'], 201);
        } else {
            return response()->json(['message' => 'Failed to change article status.'], 500);
        }
    }

    public function draftArticleList()
    {
        $userId = Auth::id();
        $articles = $this->articleService->draftArticleList($userId);
        return response()->json($articles);
    }

    public function articleDownload(Request $request, $articleId = null)
    {
        $articleFileList = $this->articleService->getFileList($articleId, $request);
        if (!empty($articleFileList)) {
            return $this->fileService->downloadMultipleAsZip($articleFileList);
        }

        return response()->json(['message' => 'No files found'], 404);
    }

    public function getItemList(Request $request){
        $item = $request->item;
        $itemList = $this->articleService->getItemList($item);
        return $itemList;
    }

    public function getTest(){
        return "Test";
    }

    public function getAuth(){
        $user =  Auth::user();
        $notificationData = $this->notificationService->getNotificationList($user);
        return response()->json($notificationData);
        // return $user;
    }
}
