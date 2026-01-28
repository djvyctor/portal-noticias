<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    // verifica se o usuario tem permissao para atualizar usuarios
    // Admin pode atualizar qualquer campo, Editor pode atualizar mas não pode alterar role
    public function authorize(): bool
    {
        $user = $this->user();
        return $user->isAdmin() || $user->isEditor();
    }

    // regras de validacao para atualizar usuario
    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;
        
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'sometimes|nullable|string|min:8',
            'role' => 'sometimes|in:admin,editor,jornalista',
        ];
    }
}
