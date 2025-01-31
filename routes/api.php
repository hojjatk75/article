<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CategoryController;
use \App\Http\Controllers\Home\CategoryController as HomeCategoryController;
use \App\Http\Controllers\Home\ArticleController as HomeArticleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:global'])->group(function (){
    Route::prefix('auth')->group(function (){
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/verify', [AuthController::class, 'verify']);
        Route::put('/complete-register', [AuthController::class, 'completeRegister']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });


    Route::middleware(['auth:sanctum', 'verified', 'completedRegister'])->group(function (){
        Route::resource('category', CategoryController::class)->except(['create', 'edit']);
        Route::resource('article', ArticleController::class)->except(['create', 'edit']);
    });


    Route::prefix('home')->group(function (){
        Route::get('/categories', [HomeCategoryController::class, 'index']);
        Route::get('/category/{category}', [HomeCategoryController::class, 'show']);

        Route::get('/articles', [HomeArticleController::class, 'index']);
        Route::get('/article/{article}', [HomeArticleController::class, 'show']);
    });
});
