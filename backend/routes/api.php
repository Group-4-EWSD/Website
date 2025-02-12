<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
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
    
    // Articles routes
    Route::prefix('articles')->group(function () {
        // Normal Routes
        Route::get('/', [ArticleController::class, 'index']); // Get all articles
        Route::post('/', [ArticleController::class, 'store']); // Create a new article
        // Route::get('/last', [ArticleController::class, 'last']); // Get the latest article
        Route::get('/{id}', [ArticleController::class, 'show']); // Get a single article by ID
        Route::patch('/{id}', [ArticleController::class, 'update']); // Update an article by ID
        Route::delete('/{id}', [ArticleController::class, 'destroy']); // Delete an article by ID
        // File Operations
        Route::post('/{id}/upload-delete-files', [ArticleController::class, 'manageFiles']); // Upload/Delete files
        Route::get('/{id}/download-zip', [ArticleController::class, 'downloadZip']); // Download article as ZIP
    });

    // Category routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']); // Get all categories
        Route::post('/', [CategoryController::class, 'store']); // Create a category
    });


    // User routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'show']);
        Route::patch('/profile', [UserController::class, 'update']);
    });
});