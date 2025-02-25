<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/getUserList', [UserController::class, 'index']);

Route::middleware([RedirectIfAuthenticated::class])->group(function () {
    Route::post('/login', [UserController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    
    

    // Category routes
    Route::prefix('categories')->group(function () {
    });


    // User routes
    Route::prefix('user')->group(function () {
    });
});
// Articles routes
Route::prefix('/articles')->group(function () {
    // Normal Routes
    Route::get('/', [ArticleController::class, 'homePageInitial']); // Get all articles
    Route::get('/{articleId}', [ActionController::class, 'articlePageInitial']);
    Route::post('/like', [ActionController::class, 'articleLike']);
    Route::post('/comment', [ActionController::class, 'articleComment']);
    Route::post('/draft-list', [ArticleController::class, 'draftArticleList']); // draft article list
    Route::post('/create', [ArticleController::class, 'articleCreate']); // create an article
});
Route::post('upload', [FileController::class, 'upload']);
Route::get('download/{fileName}', [FileController::class, 'downloadAsZip']);
Route::get('/list-files', [FileController::class, 'listFiles']); // New route