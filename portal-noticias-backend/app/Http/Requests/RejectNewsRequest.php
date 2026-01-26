<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectNewsRequest extends FormRequest
{
    // verifica se o usuario tem permissao para rejeitar noticias
    public function authorize(): bool
    {
        $user = $this->user();
        return $user->isAdmin() || $user->isEditor();
    }

    // regras de validacao para rejeitar noticia
    public function rules(): array
    {
        return [
            'rejection_reason' => 'nullable|string|max:500',
        ];
    }
}
