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
        Route::get('/myArticles', [ArticleController::class, 'myArticleInitial']); // Get all articles
        Route::get('/draftList', [ArticleController::class, 'draftArticleList']); // draft article list
        Route::get('/download/{articleId}', [ArticleController::class, 'articleDownload']);
        Route::get('/{articleId}', [ActionController::class, 'articlePageInitial']);
        Route::post('/like', [ActionController::class, 'articleLike']);
        Route::post('/comment', [ActionController::class, 'articleComment']);
        Route::delete('/commentDelete', [ActionController::class, 'articleCommentDelete']);
        Route::post('/create', [ArticleController::class, 'articleCreate']); // create an article
    });
    Route::get('/academicYearList', [ActionController::class, 'academicYearList']);

    // Category routes
    Route::prefix('/categories')->group(function () {
    });

    // User routes
    Route::prefix('/user')->group(function () {
    });
    Route::post('/logout', [UserController::class, 'logout']);

    // User routes
    Route::prefix('/notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
    });

    Route::post('/update-user-photo', [UserController::class, 'updateUserPhoto']);
    Route::get('/get-user-photo', [UserController::class, 'getUserPhoto']);
    Route::delete('/delete-user-photo', [UserController::class, 'deleteUserPhoto']);

});
Route::get('/termsCondition', [UserController::class, 'termsCondition']);

// Unnecessory, Just for testing
Route::post('upload', [FileController::class, 'upload']);
Route::get('download/{fileName}', [FileController::class, 'downloadAsZip']);
Route::get('/list-files', [FileController::class, 'listFiles']); // New route
Route::post('/list-file', [FileController::class, 'listFiles']); // New route
Route::get('/test-list-files', [FileController::class, 'listFiles']); 
Route::get('/usertype-dropdown', [UserTypeController::class, 'UserTypeDropdown']);
Route::get('/faculty-dropdown', [FacultyController::class, 'FacultyDropdown']);
