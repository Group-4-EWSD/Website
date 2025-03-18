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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/getUserList', [UserController::class, 'index']);
Route::post('/userLogin', [UserController::class, 'login']);

Route::middleware([RedirectIfAuthenticated::class])->group(function () {
    Route::post('/login', [UserController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Articles routes
    Route::prefix('/articles')->group(function () {
        // Normal Routes
        Route::get('/', [ArticleController::class, 'homePageInitial']); // Get all articles
        Route::get('/my-articles', [ArticleController::class, 'myArticleInitial']); // Get all articles
        Route::get('/artilcle-list', [ArticleController::class, 'articleList']); // Get all articles
        Route::get('/draft-list', [ArticleController::class, 'draftArticleList']); // draft article list
        Route::get('/download/{articleId}', [ArticleController::class, 'articleDownload']);
        Route::post('/like', [ActionController::class, 'articleLike']);
        Route::post('/comment', [ActionController::class, 'articleComment']);
        Route::delete('/comment-delete', [ActionController::class, 'articleCommentDelete']);
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
        Route::get('/active-user-list', [UserController::class, 'getUserList']);
        Route::patch('/update-password', [UserController::class, 'updatePassword']);
    });
    Route::post('/logout', [UserController::class, 'logout']);
    // User routes
    Route::prefix('/notifications')->group(function () {
        Route::get('/', [ActionController::class, 'getNotificationList']);
        Route::post('/', [ActionController::class, 'setNotificationView']);
    });

});
Route::get('/terms-condition', [UserController::class, 'termsCondition']);

// Unnecessory, Just for testing
Route::post('upload', [FileController::class, 'upload']);
Route::get('download/{fileName}', [FileController::class, 'downloadAsZip']);
Route::get('/list-files', [FileController::class, 'listFiles']); // New route
Route::post('/list-file', [FileController::class, 'listFiles']); // New route
Route::get('/test-list-files', [FileController::class, 'listFiles']); 
Route::get('/item-list', [ArticleController::class, 'getItemList']);