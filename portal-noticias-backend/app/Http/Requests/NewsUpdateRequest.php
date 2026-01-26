<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class NewsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->isAdmin() || $user->isEditor() || $user->isJornalista();
    }

    /**
     * Get the validation rules that apply to the request
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|max:255',
            'content' => 'string',
            'image' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'exists:categories,id',
            'status' => 'in:pending,published,rejected',
            'is_featured' => 'boolean',
        ];
    }
}
