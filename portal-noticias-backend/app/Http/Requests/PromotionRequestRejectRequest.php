<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequestRejectRequest extends FormRequest
{
    // verifica se o usuario tem permissao para rejeitar solicitacao
    // Apenas Editor e Admin podem rejeitar
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->isEditor() || $user->isAdmin());
    }

    // regras de validacao para rejeitar solicitacao
    public function rules(): array
    {
        return [
            'rejection_reason' => 'nullable|string|max:500', // motivo da rejeicao (opcional)
        ];
    }
}
