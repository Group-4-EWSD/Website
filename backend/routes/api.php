<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\FacultyController;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SystemDataController;
use App\Http\Controllers\ContactUsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/getUserList', [UserController::class, 'index']);
Route::post('/userLogin', [UserController::class, 'login']);

Route::middleware([RedirectIfAuthenticated::class])->group(function () {
    Route::post('/login', [UserController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/visit/{pageId}', [UserController::class, 'pageVisitInitial']);
    // Articles routes
    Route::prefix('/articles')->group(function () {
        // Normal Routes
        Route::get('/', [ArticleController::class, 'homePageInitial']); // Get all articles
        Route::get('/my-articles', [ArticleController::class, 'myArticleInitial']); // Get all articles
        Route::get('/coordinator', [ArticleController::class, 'coordinatorArticles']); // Get all articles
        Route::get('/manager', [ArticleController::class, 'managerArticles']); // Get all articles
        Route::get('/artilcle-list', [ArticleController::class, 'articleList']); // Get all articles
        Route::get('/draft-list', [ArticleController::class, 'draftArticleList']); // draft article list
        Route::get('/download/{articleId}', [ArticleController::class, 'articleDownload']);
        Route::post('/like', [ActionController::class, 'articleLike']);
        Route::post('/comment', [ActionController::class, 'articleComment']);
        Route::delete('/comment-delete', [ActionController::class, 'articleCommentDelete']);
        Route::post('/feedback', [ActionController::class, 'articleFeedback']);
        Route::delete('/feedback-delete', [ActionController::class, 'articleFeedbackDelete']);
        Route::post('/create', [ArticleController::class, 'articleCreateUpdate']); // create an article
        Route::post('/update', [ArticleController::class, 'articleCreateUpdate']); // create an article
        Route::get('/{articleId}', [ActionController::class, 'articlePageInitial']);
        Route::post('/change-status/{articleId}', [ArticleController::class, 'articleChangeStatus']); // update article status
    });

    // Category routes
    Route::get('/item-list', [ArticleController::class, 'getItemList']);

    // User routes
    Route::prefix('/user')->group(function () {
        Route::post('/update-photo', [UserController::class, 'updateUserPhoto']);
        Route::get('/get-photo', [UserController::class, 'getUserPhoto']);
        Route::delete('/delete-photo', [UserController::class, 'deleteUserPhoto']);
        Route::post('/edit-detail', [UserController::class, 'updateProfile']);
        Route::get('/active-user-list', [UserController::class, 'getActiveUserList']);
        Route::patch('/update-password', [UserController::class, 'updatePassword']);
        Route::get('/user-last-login', [UserController::class, 'userLastLogin']);
        // Route::get('/active-user-list', [UserController::class, 'getUserList']);
    });
    Route::post('/logout', [UserController::class, 'logout']);
    // User routes
    Route::prefix('/notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/', [NotificationController::class, 'setNotificationView']);
    });
    Route::prefix('/faculty')->group(function(){
        Route::get('/get-all-faculties', [FacultyController::class, 'listAllFaculties']);
        Route::get('/get-faculty-byID/{faculty_id}', [FacultyController::class, 'selectFacultyByID']);
        Route::post('/create', [FacultyController::class, 'createFaculty']);
        Route::patch('/update', [FacultyController::class, 'updateFaculty']);
    });

    Route::post('/user-create', [UserController::class, 'userRegister']);
    Route::get('/all-user-list', [UserController::class, 'getUserList']);
    Route::get('/get-user-bytype/{user_type}', [UserController::class, 'getUserListByType']);
    Route::post('/password-reset', [UserController::class, 'resetPassword']);
    Route::patch('/edit-user', [UserController::class, 'editUser']);

    Route::prefix('/academic-years')->group(function(){
        Route::get('/get-all-academic-years', [AcademicYearController::class, 'getAllAcademicYears']);
        Route::get('/get-byid-academic-years/{academicYearId}', [AcademicYearController::class, 'getAcademicYearById']);
        Route::post('/create', [AcademicYearController::class, 'createAcademicYear']);
        Route::patch('/update', [AcademicYearController::class, 'updateAcademicYear']);
    });

    Route::prefix('/system-data')->group(function(){
        Route::get('/list-all-system-data', [SystemDataController::class, 'listAllSysData']);
        Route::get('/byid-system-data/{system_data_id}', [SystemDataController::class, 'byidSysData']);
        Route::get('/byfaculty_system-data/{faculty_id}', [SystemDataController::class, 'byFacSysData']);
        Route::post('/create', [SystemDataController::class, 'createSysData']);
        Route::patch('/update', [SystemDataController::class, 'updateSysData']);
    });
    Route::post('upload', [FileController::class, 'upload']);
    Route::get('download/{fileName}', [FileController::class, 'downloadAsZip']);

    Route::get('/contact-us/get-all-list', [ContactUsController::class, 'getContactUsList']);
});
Route::post('/contact-us/create', [ContactUsController::class, 'createContactUs']);

// Unnecessory, Just for testing
Route::get('/list-files', [FileController::class, 'listFiles']); // New route
Route::post('/list-file', [FileController::class, 'listFiles']); // New route
Route::get('/test-list-files', [FileController::class, 'listFiles']); 
Route::get('/item-list', [ArticleController::class, 'getItemList']);

Route::get('/test', [ArticleController::class, 'getTest']);

Route::get('/testDirect', function () {
    return response()->json(['message' => 'API is working!']);
});
