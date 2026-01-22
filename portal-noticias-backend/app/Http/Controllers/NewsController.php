<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsStoreRequest;
use App\Http\Requests\NewsUpdateRequest;
use App\Models\News;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
// Importar o Trait de Autorização
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NewsController extends Controller
{
    // Usar o Trait dentro da classe
    use AuthorizesRequests;

    public function index()
    {
        // Paginação adicionada
        $news = Auth::user()->news()->paginate(10);
        return response()->json($news);
    }

    public function show(News $news)
    {
        $this->authorize('view', $news);
        
        return response()->json($news);
    }

    public function store(NewsStoreRequest $request)
    {
        $entry = $request->validated();
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = Storage::put('images', $request->file('image'));
        }

        $news = Auth::user()->news()->create([
            'title' => $entry['title'],
            'content' => $entry['content'],
            'image_path' => $imagePath,
            'slug' => \Illuminate\Support\Str::slug($entry['title']),
            'category_id' => $entry['category_id'],
            'status' => 'draft',
        ]);

        return response()->json($news, 201);
    }

    public function update(NewsUpdateRequest $request, News $news)
    {
        $this->authorize('update', $news);

        $entry = $request->validated();

        if ($request->hasFile('image')) {
            if ($news->image_path) {
                Storage::delete($news->image_path);
            }
            $imagePath = Storage::put('images', $request->file('image'));
            $news->image_path = $imagePath;
        }

        if (isset($entry['title'])) {
            $news->title = $entry['title'];
            $news->slug = \Illuminate\Support\Str::slug($entry['title']);
        }
        if (isset($entry['content'])) {
            $news->content = $entry['content'];
        }
        if (isset($entry['category_id'])) {
            $news->category_id = $entry['category_id'];
        }

        if (Auth::user()->isAdmin() || Auth::user()->isEditor()) {
            if (isset($entry['status'])) {
                $news->status = $entry['status'];
            }
        }

        $news->save();

        return response()->json($news);
    }

    public function destroy(News $news)
    {
        $this->authorize('delete', $news);

        if ($news->image_path) {
             Storage::delete($news->image_path);
        }

        $news->delete();
        return response()->json(null, 204);
    }

    public function listAll()
    {
        $news = \App\Models\News::paginate(10);
        return response()->json($news);
    }

    public function approve($id)
    {
        $news = \App\Models\News::findOrFail($id);

        $this->authorize('approve', $news);

        $news->status = 'approved';
        $news->save();

        return response()->json(['message' => 'Notícia aprovada com sucesso', 'news' => $news]);
    }

    public function feature($id)
    {
        $news = \App\Models\News::findOrFail($id);

        $this->authorize('approve', $news); // usa a nossa política de aprovação cavalo

        $news->is_featured = true;
        $news->save();

        return response()->json(['message' => 'Notícia destacada com sucesso', 'news' => $news]);
    }

    public function publicIndex()
    {
        $news = \App\Models\News::where('status', 'approved')->paginate(10);
        return response()->json($news);
    }
}