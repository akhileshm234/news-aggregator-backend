<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\UserPreferenceController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/user/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{article}', [ArticleController::class, 'show']);
    Route::get('/personalized-feed', [ArticleController::class, 'personalizedFeed']);
    
    Route::get('/preferences', [UserPreferenceController::class, 'show']);
    Route::post('/preferences', [UserPreferenceController::class, 'store']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
});

