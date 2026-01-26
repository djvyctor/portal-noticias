<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    // verifica se o usuario tem permissao para atualizar usuarios
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    // regras de validacao para atualizar usuario
    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;
        
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'sometimes|null|string|min:8',
            'role' => 'sometimes|in:admin,editor,jornalista',
        ];
    }
}
