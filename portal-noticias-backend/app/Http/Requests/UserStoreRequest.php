<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    // verifica se o usuario tem permissao para criar usuarios
    // Admin pode criar qualquer tipo, Editor pode criar apenas jornalistas
    public function authorize(): bool
    {
        $user = $this->user();
        return $user->isAdmin() || $user->isEditor();
    }

    // regras de validacao para criar usuario
    public function rules(): array
    {
        $user = $this->user();
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,editor,jornalista',
        ];

        // Editor só pode criar jornalistas
        if ($user->isEditor() && !$user->isAdmin()) {
            $rules['role'] = 'required|in:jornalista';
        }

        return $rules;
    }
}
