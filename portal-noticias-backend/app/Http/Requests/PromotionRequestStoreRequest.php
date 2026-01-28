<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequestStoreRequest extends FormRequest
{
    // verifica se o usuario tem permissao para criar solicitacao
    // Apenas jornalistas podem criar solicitacoes de promocao
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->isJornalista();
    }

    // regras de validacao para criar solicitacao
    public function rules(): array
    {
        return [
            'message' => 'required|string|min:10|max:1000', // mensagem obrigatoria, entre 10 e 1000 caracteres
        ];
    }

    // mensagens de validacao personalizadas
    public function messages(): array
    {
        return [
            'message.required' => 'Por favor, escreva uma mensagem explicando por que deseja ser editor.',
            'message.min' => 'A mensagem deve ter pelo menos 10 caracteres.',
            'message.max' => 'A mensagem não pode ter mais de 1000 caracteres.',
        ];
    }
}
