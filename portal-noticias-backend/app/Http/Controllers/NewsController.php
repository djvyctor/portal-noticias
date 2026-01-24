<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsStoreRequest;
use App\Http\Requests\NewsUpdateRequest;
use App\Models\News;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
            $imagePath = $request->file('image')->store('images', 'public');
        }

        // Define o status baseado no papel do usuário
        $status = 'draft'; // Padrão: rascunho/pendente
        
        // Se o usuário for Editor ou Admin E enviou status 'published', pode publicar direto
        if ((Auth::user()->isEditor() || Auth::user()->isAdmin()) && isset($entry['status']) && $entry['status'] === 'published') {
            $status = 'published';
        }

        $news = Auth::user()->news()->create([
            'title' => $entry['title'],
            'content' => $entry['content'],
            'image_path' => $imagePath,
            'slug' => \Illuminate\Support\Str::slug($entry['title']),
            'category_id' => $entry['category_id'],
            'status' => $status,
        ]);

        // Se foi publicado direto, define published_at
        if ($status === 'published') {
            $news->published_at = now();
            $news->save();
        }

        return response()->json($news, 201);
    }

    public function update(NewsUpdateRequest $request, News $news)
    {
        $this->authorize('update', $news);

        $entry = $request->validated();

        if ($request->hasFile('image')) {
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }
            $imagePath = $request->file('image')->store('images', 'public');
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
             Storage::disk('public')->delete($news->image_path);
        }

        $news->delete();
        return response()->json(null, 204);
    }

    public function listAll()
    {
        $news = \App\Models\News::with(['user:id,name', 'category:id,name,slug'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return response()->json($news);
    }

    public function approve($id)
    {
        $news = \App\Models\News::findOrFail($id);

        $this->authorize('approve', $news);

        $news->status = 'published';
        $news->published_at = now();
        $news->save();

        // Recarrega a notícia com relacionamentos para garantir que está atualizada
        $news->refresh();
        $news->load(['user', 'category']);

        return response()->json(['message' => 'Notícia aprovada com sucesso', 'news' => $news]);
    }

    public function feature($id)
    {
        $news = \App\Models\News::findOrFail($id);

        $this->authorize('approve', $news); // usa a nossa política de aprovação cavalo

        // Toggle: se já está destacada, remove; se não está, destaca
        $news->is_featured = !$news->is_featured;
        $news->save();

        // Recarrega a notícia para garantir que está atualizada
        $news->refresh();
        $news->load(['user', 'category']);

        $message = $news->is_featured 
            ? 'Notícia destacada com sucesso' 
            : 'Destaque removido com sucesso';

        return response()->json(['message' => $message, 'news' => $news]);
    }

    public function publicIndex()
    {
        $news = News::where('status', 'published')
            ->with(['user:id,name', 'category:id,name,slug'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($news);
    }

    public function showBySlug($slug)
    {
        $news = News::where('slug', $slug)
            ->where('status', 'published')
            ->with(['user', 'category'])
            ->firstOrFail();

        return response()->json($news);
    }

    public function getByCategory($categorySlug)
    {
        $category = \App\Models\Category::where('slug', $categorySlug)->firstOrFail();
        $news = News::where('category_id', $category->id)
            ->where('status', 'published')
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($news);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
        ]);

        $query = $request->input('q');

        $news = News::where('status', 'published')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($news);
    }

    public function featured()
    {
        $news = News::where('status', 'published')
            ->where('is_featured', true)
            ->with(['user:id,name', 'category:id,name,slug'])
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Retorna array vazio se não houver notícias, mas sempre retorna JSON válido
        return response()->json($news);
    }
}