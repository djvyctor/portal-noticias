<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionRequestStoreRequest;
use App\Http\Requests\PromotionRequestRejectRequest;
use App\Models\PromotionRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PromotionRequestController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    // lista solicitacoes pendentes (apenas Editor/Admin)
    public function index()
    {
        $currentUser = Auth::user();
        
        // Apenas Editor e Admin podem ver solicitacoes
        if (!$currentUser->isEditor() && !$currentUser->isAdmin()) {
            return $this->unauthorizedResponse();
        }

        $requests = PromotionRequest::with(['user:id,name,email', 'reviewer:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($requests);
    }

    // lista solicitacoes do usuario logado (jornalista ve suas proprias solicitacoes)
    public function myRequests()
    {
        $user = Auth::user();
        
        $requests = PromotionRequest::where('user_id', $user->id)
            ->with(['reviewer:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }

    // cria uma nova solicitacao de promocao (apenas jornalistas)
    public function store(PromotionRequestStoreRequest $request)
    {
        $user = Auth::user();

        // Verifica se ja existe uma solicitacao pendente
        $existingRequest = PromotionRequest::where('user_id', $user->id)
            ->where('status', PromotionRequest::STATUS_PENDING)
            ->first();

        if ($existingRequest) {
            return $this->errorResponse('Você já possui uma solicitação pendente. Aguarde a resposta antes de criar uma nova.', 400);
        }

        // Verifica se o usuario ja e editor ou admin
        if (!$user->isJornalista()) {
            return $this->errorResponse('Apenas jornalistas podem solicitar promoção.', 403);
        }

        $promotionRequest = PromotionRequest::create([
            'user_id' => $user->id,
            'message' => $request->input('message'),
            'status' => PromotionRequest::STATUS_PENDING,
        ]);

        return response()->json($promotionRequest, 201);
    }

    // aprova uma solicitacao de promocao (Editor/Admin)
    public function approve($id)
    {
        $promotionRequest = PromotionRequest::findOrFail($id);
        $currentUser = Auth::user();

        // Verifica se o usuario pode aprovar (Editor/Admin)
        if (!$currentUser->isEditor() && !$currentUser->isAdmin()) {
            return $this->unauthorizedResponse();
        }

        // Verifica se a solicitacao ja foi processada
        if ($promotionRequest->status !== PromotionRequest::STATUS_PENDING) {
            return $this->errorResponse('Esta solicitação já foi processada.', 400);
        }

        // Aprova a solicitacao e promove o usuario para editor
        $promotionRequest->status = PromotionRequest::STATUS_APPROVED;
        $promotionRequest->reviewed_by = $currentUser->id;
        $promotionRequest->reviewed_at = now();
        $promotionRequest->save();

        // Promove o usuario para editor
        $user = $promotionRequest->user;
        $user->role = User::ROLE_EDITOR;
        $user->save();

        return $this->successResponse([], 'Solicitação aprovada com sucesso! O usuário foi promovido a Editor.');
    }

    // rejeita uma solicitacao de promocao (Editor/Admin)
    public function reject(PromotionRequestRejectRequest $request, $id)
    {
        $promotionRequest = PromotionRequest::findOrFail($id);
        $currentUser = Auth::user();

        // Verifica se o usuario pode rejeitar (Editor/Admin)
        if (!$currentUser->isEditor() && !$currentUser->isAdmin()) {
            return $this->unauthorizedResponse();
        }

        // Verifica se a solicitacao ja foi processada
        if ($promotionRequest->status !== PromotionRequest::STATUS_PENDING) {
            return $this->errorResponse('Esta solicitação já foi processada.', 400);
        }

        // Rejeita a solicitacao
        $promotionRequest->status = PromotionRequest::STATUS_REJECTED;
        $promotionRequest->reviewed_by = $currentUser->id;
        $promotionRequest->rejection_reason = $request->input('rejection_reason');
        $promotionRequest->reviewed_at = now();
        $promotionRequest->save();

        return $this->successResponse([], 'Solicitação rejeitada com sucesso.');
    }

    // exibe detalhes de uma solicitacao
    public function show($id)
    {
        $promotionRequest = PromotionRequest::with(['user:id,name,email', 'reviewer:id,name'])
            ->findOrFail($id);
        
        $currentUser = Auth::user();

        // Jornalista so pode ver suas proprias solicitacoes
        // Editor/Admin podem ver todas
        if ($currentUser->isJornalista() && $promotionRequest->user_id !== $currentUser->id) {
            return $this->unauthorizedResponse();
        }

        return response()->json($promotionRequest);
    }
}
