<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class NewsStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Prepara os dados para validação.
     * Isso roda ANTES das rules().
     */
    protected function prepareForValidation(): void
    {
        // Se houver um título, geramos o slug e o adicionamos ao request
        if ($this->has('title')) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'image'       => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'slug'        => 'required|string|unique:news,slug',
        ];
    }
}