<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectNewsRequest extends FormRequest
{
    // verifica se o usuário tem permissão para rejeitar notícias
    public function authorize(): bool
    {
        $user = $this->user();
        return $user->isAdmin() || $user->isEditor();
    }

    // regras de validação para rejeitar notícia
    public function rules(): array
    {
        return [
            'rejection_reason' => 'nullable|string|max:500',
        ];
    }
}
