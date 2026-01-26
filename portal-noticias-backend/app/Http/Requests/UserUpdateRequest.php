<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    // verifica se o usuário tem permissão para atualizar usuários
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    // regras de validação para atualizar usuário
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
