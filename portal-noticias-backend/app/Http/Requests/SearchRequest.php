<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    // permite busca publica
    public function authorize(): bool
    {
        return true;
    }

    // regras de validação para busca
    public function rules(): array
    {
        return [
            'q' => 'required|string|min:2|max:255',
        ];
    }
}
