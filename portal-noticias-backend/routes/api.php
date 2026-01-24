<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CategoryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::apiResource('/user/news', NewsController::class);
    Route::patch('/news/{id}/approve', [\App\Http\Controllers\NewsController::class, 'approve']);
    Route::patch('/news/{id}/feature', [\App\Http\Controllers\NewsController::class, 'feature']);
    Route::patch('/news/{id}/reject', [\App\Http\Controllers\NewsController::class, 'reject']);
    
    // Notícias pendentes e rejeitadas (Admin e Editor)
    Route::get('/news/pending', [\App\Http\Controllers\NewsController::class, 'pending']);
    Route::get('/news/rejected', [\App\Http\Controllers\NewsController::class, 'rejected']);
    Route::get('/news/rejected/{userId}', [\App\Http\Controllers\NewsController::class, 'rejected']);
    
    // Gerenciamento de usuários (apenas Admin)
    Route::apiResource('/admin/users', \App\Http\Controllers\UserController::class);
    Route::post('/me/name', [\App\Http\Controllers\UserController::class, 'updateName']);
});

// Rotas públicas para visualização
Route::get('/news/public', [\App\Http\Controllers\NewsController::class, 'publicIndex']);
Route::get('/news/featured', [NewsController::class, 'featured']);
Route::get('/news/carousel', [NewsController::class, 'carousel']);
Route::get('/news/daily', [NewsController::class, 'dailyNews']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/news/all', [NewsController::class, 'listAll']);
});
Route::get('/news/category/{categorySlug}', [NewsController::class, 'getByCategory']);
Route::get('/news/search', [NewsController::class, 'search']);
Route::get('/news/{slug}', [NewsController::class, 'showBySlug']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);