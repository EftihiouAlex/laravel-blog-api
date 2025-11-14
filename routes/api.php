<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/posts', [PostsController::class, 'showAllPosts']);
Route::get('/categories', [CategoriesController::class, 'getAllCategories']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/posts', [PostsController::class, 'newPost']);
    Route::get('/posts/{post}/{slug}', [PostsController::class, 'showPost']);
    Route::put('/posts/{post}', [PostsController::class, 'update']);
    Route::delete('/posts/{post}', [PostsController::class, 'deletePostById']);
    Route::get('/users/{user}/posts', [PostsController::class, 'postsByUser']);
    Route::get('/users/{user}/comments', [CommentController::class, 'commentsByUser']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'newComment']);
});
