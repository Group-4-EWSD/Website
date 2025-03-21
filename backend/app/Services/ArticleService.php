<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Repositories\ActionRepository;
use App\Repositories\FileRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService
{
    protected $articleRepository;
    protected $actionRepository;
    protected $userRepository;
    protected $fileRepository;
    protected $fileService;

    public function __construct(ArticleRepository $articleRepository, ActionRepository $actionRepository, UserRepository $userRepository, FileRepository $fileRepository, FileService $fileService)
    {
        $this->articleRepository = $articleRepository;
        $this->actionRepository = $actionRepository;
        $this->userRepository = $userRepository;
        $this->fileRepository = $fileRepository;
        $this->fileService = $fileService;
    }

    public function getGuestHomePageData(){
        
    }
    public function getStudentHomePageData($userId, $request)
    {
        return [
            'countData' => $this->articleRepository->getStudentHomeCountData(),
            'allArticles' => $this->articleRepository->getAllArticles(0, $userId, $request)->get()
        ];
    }
    public function getCoordinatorHomePageData($userId, $request){
        return [
            'countData' => $this->articleRepository->getCoordinatorHomeCountData($userId),
            'allArticles' => $this->articleRepository->getAllArticles(3, $request)->get(),
            'articlesPerYear' => $this->articleRepository->getArticlePerYear(),
            'guestList' => $this->userRepository->getGuestList()
        ];
    }
    public function getManagerHomePageData(){
        
    }
    public function getAdminHomePageData(){
        
    }

    public function getMyArticles($userId, $facultyId, $request)
    {
        $deadlines = $this->articleRepository->getDeadlines($facultyId);
        $countData = $this->articleRepository->getStudentHomeCountData();
        $myArticles = $this->articleRepository->getAllArticles(1,$userId, $request);
        $latestArticles = $myArticles->orderBy('created_at', 'desc')->take(3)->get();
        return [
            'preUploadDeadline' => $deadlines->pre_submission_date,
            'actualUploadDeadline' => $deadlines->actual_submission_date,
            'countData' => $countData,
            'latestArticles' => $latestArticles,
            'myArticles' => $myArticles->get()
        ];
    }

    public function getCoordinatorArticles($facultyId, $request)
    {
        $countData = $this->articleRepository->getCoordinatorCountData($facultyId);
        $articles = $this->articleRepository->getAllArticles(3, $facultyId, $request)->orderBy('created_at', 'desc')->get();
        return [
            'totalSubmissions' => $countData['totalSubmissions'], // Correct array access
            'pendingReview' => $countData['pendingReview'],
            'approvedArticles' => $countData['approvedArticles'],
            'rejectedArticles' => $countData['rejectedArticles'],
            'articles' => $articles
        ];
    }

    public function getArticleList($user_id, $faculty_id, $request){
        return [
            'countData' => $this->articleRepository->getCountDataByFaculty($faculty_id),
            'articleList' => $this->articleRepository->getAllArticles(3, $faculty_id, $request),
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
            }

            DB::commit();
            return ['success' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function changeArticleStatus($userId, $articleId, $request){
        return $this->articleRepository->createActivity( $articleId, $userId,$request);
    }

    public function draftArticleList($userId){
        return $this->articleRepository->getAllArticles(2, $userId)->get();
    }

    public function getFileList($articleId){
        return $this->articleRepository->getFileList($articleId);
    }

    public function getItemList($item){
        return $this->articleRepository->getItemList($item);
    }
}
