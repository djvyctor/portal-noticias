<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    // verifica se o usuário tem permissão para criar usuários
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    // regras de validação para criar usuário
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,editor,jornalista',
        ];
    }
}
