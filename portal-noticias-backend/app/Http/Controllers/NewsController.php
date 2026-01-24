<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsStoreRequest;
use App\Http\Requests\NewsUpdateRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NewsController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $news = Auth::user()->news()->with(['category'])->paginate(10);
        return NewsResource::collection($news);
    }

    public function show(News $news)
    {
        $this->authorize('view', $news);
        $news->load(['user', 'category']);
        return new NewsResource($news);
    }

    public function store(NewsStoreRequest $request)
    {
        $entry = $request->validated();
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $status = 'pending';
        if ((Auth::user()->isEditor() || Auth::user()->isAdmin()) && isset($entry['status']) && $entry['status'] === 'published') {
            $status = 'published';
        }
        $authorName = ucwords(strtolower(trim(Auth::user()->name)));

        $news = Auth::user()->news()->create([
            'title' => $entry['title'],
            'content' => $entry['content'],
            'image_path' => $imagePath,
            'slug' => \Illuminate\Support\Str::slug($entry['title']),
            'category_id' => $entry['category_id'],
            'status' => $status,
            'author_name' => $authorName,
        ]);

        if ($status === 'published') {
            $news->published_at = now();
            $news->save();
        }

        return (new NewsResource($news))->response()->setStatusCode(201);
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
            if (array_key_exists('is_featured', $entry)) {
                $news->is_featured = (bool) $entry['is_featured'];
            }
        } else {
            if ($news->status === 'rejected') {
                $news->status = 'pending';
                $news->rejection_reason = null;
            }
        }

        $news->save();

        return response()->json([
            'message' => 'Notícia atualizada com sucesso',
            'success' => true
        ]);
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
        return NewsResource::collection($news);
    }

    public function approve($id)
    {
        $news = \App\Models\News::findOrFail($id);

        $this->authorize('approve', $news);

        $news->status = 'published';
        $news->published_at = now();
        $news->save();

        return response()->json([
            'message' => 'Notícia aprovada com sucesso',
            'success' => true
        ]);
    }

    public function feature($id)
    {
        $news = \App\Models\News::findOrFail($id);

        $this->authorize('approve', $news);
        $news->is_featured = !$news->is_featured;
        $news->save();

        $message = $news->is_featured 
            ? 'Notícia destacada com sucesso' 
            : 'Destaque removido com sucesso';

        return response()->json([
            'message' => $message,
            'success' => true,
            'is_featured' => $news->is_featured
        ]);
    }

    public function publicIndex()
    {
        $news = News::where('status', 'published')
            ->with(['user:id,name', 'category:id,name,slug'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return NewsResource::collection($news);
    }

    public function showBySlug($slug)
    {
        $news = News::where('slug', $slug)
            ->where('status', 'published')
            ->with(['user', 'category'])
            ->firstOrFail();

        return new NewsResource($news);
    }

    public function getByCategory($categorySlug)
    {
        $category = \App\Models\Category::where('slug', $categorySlug)->firstOrFail();
        $news = News::where('category_id', $category->id)
            ->where('status', 'published')
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return NewsResource::collection($news);
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

        return NewsResource::collection($news);
    }

    public function reject($id)
    {
        $news = \App\Models\News::findOrFail($id);

        $this->authorize('reject', $news);
        $news->status = 'rejected';
        $news->rejection_reason = request('rejection_reason') ?? 'Não informado';
        $news->save();

        return response()->json([
            'message' => 'Notícia rejeitada com sucesso',
            'success' => true
        ]);
    }

    public function pending(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $perPage = (int) ($request->input('per_page') ?: 15);
        $perPage = $perPage >= 1 && $perPage <= 50 ? $perPage : 15;

        $news = \App\Models\News::where('status', 'pending')
            ->with(['user:id,name,email', 'category:id,name,slug'])
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);

        return NewsResource::collection($news);
    }

    public function rejected($userId = null)
    {
        if (!$userId) {
            $userId = Auth::user()->id;
        }
        if (Auth::user()->id != $userId && !Auth::user()->isAdmin() && !Auth::user()->isEditor()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $news = \App\Models\News::where('status', 'rejected')
            ->where('user_id', $userId)
            ->with(['category:id,name,slug', 'user:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return NewsResource::collection($news);
    }

    public function carousel()
    {
        $news = News::where('status', 'published')
            ->where('is_featured', true)
            ->with(['user:id,name', 'category:id,name,slug'])
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        return NewsResource::collection($news);
    }

    public function dailyNews()
    {
        $news = News::where('status', 'published')
            ->where('is_featured', false)
            ->with(['user:id,name', 'category:id,name,slug'])
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        return NewsResource::collection($news);
    }

    public function featured()
    {
        $news = News::where('status', 'published')
            ->where('is_featured', true)
            ->with(['user:id,name', 'category:id,name,slug'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return NewsResource::collection($news);
    }
}