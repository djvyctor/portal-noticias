<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    // listar todas as categorias disponiveis
    public function index(): JsonResponse
    {
        $categories = Category::withCount('news')
            ->orderBy('name')
            ->get();
        
        return response()->json($categories);
    }
    
    // mostra detalhes de uma categoria especifica
    public function show(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->withCount('news')
            ->firstOrFail();
        
        return response()->json($category);
    }
}