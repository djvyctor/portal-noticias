<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CategoryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::apiResource('/user/news', NewsController::class); // Cria automaticamente as rotas para index, show, store, update, destroy
    Route::patch('/news/{id}/approve', [\App\Http\Controllers\NewsController::class, 'approve']);
    Route::patch('/news/{id}/feature', [\App\Http\Controllers\NewsController::class, 'feature']);
});

Route::get('/news', [NewsController::class, 'listAll']);
Route::get('/news/public', [\App\Http\Controllers\NewsController::class, 'publicIndex']);
Route::get('/news/{slug}', [NewsController::class, 'showBySlug']);
Route::get('/news/category/{categorySlug}', [NewsController::class, 'getByCategory']);
Route::get('/news/search', [NewsController::class, 'search']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);
Route::get('/news/featured', [NewsController::class, 'featured']);