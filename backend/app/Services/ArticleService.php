<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Repositories\ActionRepository;
use App\Repositories\FacultyRepository;
use App\Repositories\FileRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService
{
    protected $articleRepository;
    protected $actionRepository;
    protected $userRepository;
    protected $facultyRepository;
    protected $fileRepository;
    protected $notificationRepository;
    protected $fileService;

    public function __construct(ArticleRepository $articleRepository, ActionRepository $actionRepository, UserRepository $userRepository, FileRepository $fileRepository, FileService $fileService, FacultyRepository $facultyRepository, NotificationRepository $notificationRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->actionRepository = $actionRepository;
        $this->userRepository = $userRepository;
        $this->facultyRepository = $facultyRepository;
        $this->fileRepository = $fileRepository;
        $this->fileService = $fileService;
        $this->notificationRepository = $notificationRepository;
    }

    public function getGuestHomePageData($userId, $facultyId, $request){
        return [
            'prev_login' => $this->articleRepository->getPreviousLogin($userId),
            'allArticles' => $this->articleRepository->getAllArticles(5, null, $request)->get(),
            'articlesPerYear' => $this->articleRepository->getArticlePerYear($facultyId),
            'facultyList' => $this->facultyRepository->getfacultyList(),
            'publishedList' => $this->articleRepository->getPublishedList()
        ];
    }

    public function getStudentHomePageData($userId, $request)
    {
        return [
            'prev_login' => $this->articleRepository->getPreviousLogin($userId),
            'countData' => $this->articleRepository->getStudentHomeCountData(),
            'allArticles' => $this->articleRepository->getAllArticles(0, $userId, $request)->get()
        ];
    }
    public function getCoordinatorHomePageData($userId, $facultyId, $request){
        return [
            'prev_login' => $this->articleRepository->getPreviousLogin($userId),
            'submission_status' => $this->articleRepository->getSubmissionStatus($facultyId),
            'remaining_final_publish' => $this->articleRepository->getRemainingFinalPublish($facultyId),
            'current_system_data' => $this->articleRepository->getCurrentSystemData($facultyId),
            'countData' => $this->articleRepository->getCoordinatorManagerHomeCountData($facultyId),
            'allArticles' => $this->articleRepository->getAllArticles(3, $facultyId, $request)->get(),
            'articlesPerYear' => $this->articleRepository->getArticlePerYear($facultyId),
            'guestList' => $this->userRepository->getGuestList()
        ];
    }
    public function getManagerHomePageData($userId){
        return [
            'prev_login' => $this->articleRepository->getPreviousLogin($userId),
            'countData' => $this->articleRepository->getCoordinatorManagerHomeCountData(),
            'articlesPerYear' => $this->articleRepository->getArticlePerYear(),
            'memberList' => $this->userRepository->getMemberList(),
            'guestList' => $this->userRepository->getGuestList()
        ];
    }
    public function getAdminReports(){
        return [
            'mostViewedPages' => $this->userRepository->getMostViewedPageVisit(),
            'activeUserList' => $this->userRepository->getActiveUserList(null),
            'browserList' => $this->userRepository->getMostUsedBrowserList()
        ];
    }

    public function getMyArticles($userId, $facultyId, $request)
    {
        $deadlines = $this->articleRepository->getDeadlines($facultyId);
        $countData = $this->articleRepository->getStudentHomeCountData();
        $myArticles = $this->articleRepository->getAllArticles(1,$userId, $request);
        $myArticlesList = $this->articleRepository->limitArticleList($request,$myArticles)->orderBy('art.created_at', 'desc')->get();
        $latestArticles = $myArticles->orderBy('art.created_at', 'desc')->take(3)->get();
        return [
            'preUploadDeadline' => $deadlines->pre_submission_date,
            'actualUploadDeadline' => $deadlines->actual_submission_date,
            'countData' => $countData,
            'latestArticles' => $latestArticles,
            'myArticles' => $myArticlesList,
            'myArticlesCount' => $myArticles->get()->count()
        ];
    }

    public function getCoordinatorArticles($facultyId, $request)
    {
        $countData = $this->articleRepository->getCoordinatorCountData($facultyId);
        $articles = $this->articleRepository->getAllArticles(3, $facultyId, $request);
        $articleList = $this->articleRepository->limitArticleList($request,$articles)->orderBy('art.created_at', 'desc')->get();
        return [
            'totalSubmissions' => $countData['totalSubmissions'], // Correct array access
            'pendingReview' => $countData['pendingReview'],
            'approvedArticles' => $countData['approvedArticles'],
            'rejectedArticles' => $countData['rejectedArticles'],
            'articlesCount' => $articles->get()->count(),
            'articles' => $articleList,
        ];
    }

    public function getManagerArticles($request)
    {
        $articles = $this->articleRepository->getAllArticles(4, null, $request);
        $articleList = $this->articleRepository->limitArticleList($request,$articles)->orderBy('art.created_at', 'desc')->get();
        return [
            'articles' => $articleList,
            'articlesCount' => $articles->get()->count(),
        ];
    }

    public function getGuestArticles($request)
    {
        $articles = $this->articleRepository->getAllArticles(5, null, $request);
        $articleList = $this->articleRepository->limitArticleList($request,$articles)->orderBy('art.created_at', 'desc')->get();
        return [
            'articles' => $articleList,
            'articlesCount' => $articles->get()->count(),
        ];
    }

    public function createUpdateArticle($userId, $request)
    {
        
        $systemId = $this->articleRepository->getSystemId($userId);
        DB::beginTransaction();

        try {
            // Generate unique IDs
            $articleId = "";
            if(!empty($request->article_id)){
                $articleId = $request->article_id;
            }else{
                $articleId = Str::uuid();
            }
            // Save article
            $this->articleRepository->createOrUpdateArticle($articleId, $userId, $systemId, $request);
            // Process and save article details (file uploads)
            if (!empty($request->article_id)) {
                $articleFiles = $this->actionRepository->getArticleDetailFiles($articleId);
                $fileList = [];
                foreach ($articleFiles as $file) {
                    $fileList[] = $file->file_name;
                }
                $filesToBeDeleted = [];
                if(empty($request->article_remaining_files)){
                    $filesToBeDeleted = $fileList;
                }else{
                    $filesToBeDeleted = array_diff($fileList, $request->article_remaining_files);
                }
                foreach ($filesToBeDeleted as $file_name) {
                    $this->fileService->deleteFile($file_name);
                    $this->articleRepository->deleteArticleDetail($file_name);
                }
            }
            if ($request->hasFile('article_details')) {
                foreach ($request->file('article_details') as $key => $file) {
                    if ($file->isValid()) { 
                        // Generate filename
                        $fileName = uniqid() . '_' . $request->article_title . '.' . $file->getClientOriginalExtension();
                        if( $file->getClientOriginalExtension() == "docx" ){
                            $filePath = 'documents/' . $fileName;
                            $this->fileRepository->uploadToS3($filePath, $file->getRealPath());
                            // $filePath = 'https://ewsdcloud.s3.amazonaws.com/documents/' . $fileName;
                        } else {
                            $filePath = $this->fileService->uploadFile($file)['file_path']; 
                            // $filePath = $photoPath ? "https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/{$photoPath}" : null;
                        }
                        // Save article details
                        $this->articleRepository->createArticleDetail($articleId, $filePath, $fileName, $file->getClientOriginalExtension());
                        
                    }
                }
            }
            if (empty($request->article_id)) {
                $this->articleRepository->createActivity($articleId, $userId, $request);
                $this->notificationRepository->setNotification('4', $articleId);
            }else{
                $this->notificationRepository->setNotification('5', $request->articleId);
            }

            DB::commit();
            return ['success' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function changeArticleStatus($userId, $articleId, $request){
        if($articleId != null){
            $this->notificationRepository->setNotification('6', $articleId);
            $this->articleRepository->createActivity( $articleId, $userId,$request);
        }else{
            foreach ($request->articleIdList as $eachArticleId) {
                $this->notificationRepository->setNotification('6', $eachArticleId);
                $this->articleRepository->createActivity($eachArticleId, $userId,$request);
            }
        }
        return true;
    }

    public function draftArticleList($userId){
        $articles = $this->articleRepository->getAllArticles(2, $userId);
        return [
            'draftArticles' => $articles->get(),
            'draftArticlesCount' => $articles->get()->count()
        ];
    }

    public function getFileList($articleId, $request){
        return $this->articleRepository->getFileList($articleId, $request);
    }

    public function getItemList($item){
        return $this->articleRepository->getItemList($item);
    }

    public function getTopArticles(){
        return $this->articleRepository->getTopArticles();
    }
}
