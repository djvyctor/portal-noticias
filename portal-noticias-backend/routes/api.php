<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PromotionRequestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// rotas publicas de autenticacao
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// rotas que precisam de autenticacao
Route::middleware('auth:sanctum')->group(function () {
    // autenticacao do usuario logado
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/me/name', [UserController::class, 'updateName']);
    
    // CRUD de noticias do usuario logado
    Route::apiResource('/user/news', NewsController::class);
    
    // acoes de noticias (admin e editor)
    Route::patch('/news/{id}/approve', [NewsController::class, 'approve']);
    Route::patch('/news/{id}/feature', [NewsController::class, 'feature']);
    Route::patch('/news/{id}/reject', [NewsController::class, 'reject']);
    
    // listagens de noticias (admin e editor)
    Route::get('/news/pending', [NewsController::class, 'pending']);
    Route::get('/news/rejected', [NewsController::class, 'rejected']);
    Route::get('/news/rejected/{userId}', [NewsController::class, 'rejected']);
    Route::get('/news/all', [NewsController::class, 'listAll']);
    
    // gerenciamento de usuarios (admin)
    Route::apiResource('/admin/users', UserController::class);
    
    // solicitacoes de promocao (jornalista para editor)
    Route::get('/promotion-requests/my', [PromotionRequestController::class, 'myRequests']); // jornalista ve suas solicitacoes
    Route::post('/promotion-requests', [PromotionRequestController::class, 'store']); // jornalista cria solicitacao
    Route::get('/promotion-requests', [PromotionRequestController::class, 'index']); // editor/admin ve todas
    Route::get('/promotion-requests/{id}', [PromotionRequestController::class, 'show']); // ver detalhes
    Route::patch('/promotion-requests/{id}/approve', [PromotionRequestController::class, 'approve']); // editor/admin aprova
    Route::patch('/promotion-requests/{id}/reject', [PromotionRequestController::class, 'reject']); // editor/admin rejeita
});

// rotas publicas de visualizacao
Route::get('/news/public', [NewsController::class, 'publicIndex']);
Route::get('/news/featured', [NewsController::class, 'featured']);
Route::get('/news/carousel', [NewsController::class, 'carousel']);
Route::get('/news/daily', [NewsController::class, 'dailyNews']);
Route::get('/news/category/{categorySlug}', [NewsController::class, 'getByCategory']);
Route::get('/news/search', [NewsController::class, 'search']);
Route::get('/news/{slug}', [NewsController::class, 'showBySlug']);

// rotas publicas de categorias
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);