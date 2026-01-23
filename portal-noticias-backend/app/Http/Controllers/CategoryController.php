<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('news')
            ->orderBy('name')
            ->get();
        
        return response()->json($categories);
    }
    
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->withCount('news')
            ->firstOrFail();
        
        return response()->json($category);
    }
}