<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsStoreRequest;
use App\Http\Requests\NewsUpdateRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\RejectNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NewsController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    // lista todas as noticias do usuario logado
    public function index()
    {
        $news = Auth::user()->news()
            ->with(['category'])
            ->paginate(News::PAGINATION_DEFAULT);
        
        return NewsResource::collection($news);
    }

    // exibe detalhes de uma noticia
    public function show(News $news)
    {
        $this->authorize('view', $news);
        $news->load(['user', 'category']);
        
        return new NewsResource($news);
    }

    // cria uma noticia, se user for admin ou editor pode publicar direto -> jornalista precisa de aprovação
    public function store(NewsStoreRequest $request)
    {
        $entry = $request->validated();
        $user = Auth::user();
        
        $imagePath = $this->handleImageUpload($request);
        $status = $this->determineStatus($user, $entry);
        $authorName = format_name($user->name);

        $news = $user->news()->create([
            'title' => $entry['title'],
            'content' => $entry['content'],
            'image_path' => $imagePath,
            'slug' => \Illuminate\Support\Str::slug($entry['title']),
            'category_id' => $entry['category_id'],
            'status' => $status,
            'author_name' => $authorName,
        ]);

        if ($status === News::STATUS_PUBLISHED) {
            $news->published_at = now();
            $news->save();
        }

        return (new NewsResource($news))->response()->setStatusCode(201);
    }

    // atualiza uma noticia
    public function update(NewsUpdateRequest $request, News $news)
    {
        $this->authorize('update', $news);

        $entry = $request->validated();
        $user = Auth::user();

        $this->updateImage($request, $news);
        $this->updateBasicFields($entry, $news);
        $this->updateStatusAndFeatured($user, $entry, $news);

        $news->save();

        return $this->successResponse([], 'Notícia atualizada com sucesso');
    }

    // deleta noticia e remove arquivo de imagem
    public function destroy(News $news)
    {
        $this->authorize('delete', $news);

        if ($news->image_path) {
            Storage::disk('public')->delete($news->image_path);
        }

        $news->delete();
        
        return response()->json(null, 204);
    }

    // admin ve tudo
    public function listAll()
    {
        $news = News::withRelations()
            ->orderBy('created_at', 'desc')
            ->paginate(News::PAGINATION_DEFAULT);
        
        return NewsResource::collection($news);
    }

    // aprova noticia pendente
    public function approve($id)
    {
        $news = News::findOrFail($id);
        $this->authorize('approve', $news);

        $news->status = News::STATUS_PUBLISHED;
        $news->published_at = now();
        $news->save();

        return $this->successResponse([], 'Notícia aprovada com sucesso');
    }

    // colocar ou tirar destaque
    public function feature($id)
    {
        $news = News::findOrFail($id);
        $this->authorize('approve', $news);
        
        $news->is_featured = !$news->is_featured;
        $news->save();

        $message = $news->is_featured 
            ? 'Notícia destacada com sucesso' 
            : 'Destaque removido com sucesso';

        return $this->successResponse(
            ['is_featured' => $news->is_featured],
            $message
        );
    }

    // home do site
    public function publicIndex()
    {
        $news = News::published()
            ->withRelations()
            ->orderBy('created_at', 'desc')
            ->paginate(News::PAGINATION_DEFAULT);

        return NewsResource::collection($news);
    }

    // acessar noticia pela slug
    public function showBySlug($slug)
    {
        $news = News::published()
            ->where('slug', $slug)
            ->with(['user', 'category'])
            ->firstOrFail();

        return new NewsResource($news);
    }

    // filtra noticias por categoria
    public function getByCategory($categorySlug)
    {
        $category = \App\Models\Category::where('slug', $categorySlug)->firstOrFail();
        
        $news = News::published()
            ->where('category_id', $category->id)
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(News::PAGINATION_DEFAULT);

        return NewsResource::collection($news);
    }

    // busca no site
    public function search(SearchRequest $request)
    {
        $query = $request->input('q');

        $news = News::published()
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(News::PAGINATION_DEFAULT);

        return NewsResource::collection($news);
    }

    // rejeita noticia com algum motivo
    public function reject(RejectNewsRequest $request, $id)
    {
        $news = News::findOrFail($id);
        $this->authorize('reject', $news);
        
        $news->status = News::STATUS_REJECTED;
        $news->rejection_reason = $request->input('rejection_reason', 'Não informado');
        $news->save();

        return $this->successResponse([], 'Notícia rejeitada com sucesso');
    }

    // lista de espera para admin e editor
    public function pending(Request $request)
    {
        $this->ensureAdminOrEditor();

        $perPage = $this->getValidPerPage($request->input('per_page'), News::PAGINATION_ADMIN);

        $news = News::pending()
            ->with(['user:id,name,email', 'category:id,name,slug'])
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);

        return NewsResource::collection($news);
    }

    // lista de reprovados
    public function rejected($userId = null)
    {
        $userId = $userId ?? Auth::user()->id;
        
        if (Auth::user()->id != $userId && !$this->isAdminOrEditor()) {
            return $this->unauthorizedResponse();
        }

        $news = News::rejected()
            ->where('user_id', $userId)
            ->with(['category:id,name,slug', 'user:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(News::PAGINATION_ADMIN);

        return NewsResource::collection($news);
    }

    // coloca as 5 noticias em destaque no carrossel da home
    public function carousel()
    {
        $news = News::published()
            ->featured()
            ->withRelations()
            ->orderBy('published_at', 'desc')
            ->limit(News::CAROUSEL_LIMIT)
            ->get();

        return NewsResource::collection($news);
    }

    // retorna noticias comuns
    public function dailyNews()
    {
        $news = News::published()
            ->notFeatured()
            ->withRelations()
            ->orderBy('published_at', 'desc')
            ->paginate(News::PAGINATION_ADMIN);

        return NewsResource::collection($news);
    }

    // lista pagina de destaques
    public function featured()
    {
        $news = News::published()
            ->featured()
            ->withRelations()
            ->orderBy('published_at', 'desc')
            ->paginate(News::PAGINATION_DEFAULT);

        return NewsResource::collection($news);
    }

    // métodos privados auxiliares

    // determina o status inicial da noticia baseado no papel do usuario
    private function determineStatus($user, array $entry): string
    {
        if (($user->isEditor() || $user->isAdmin()) 
            && isset($entry['status']) 
            && $entry['status'] === News::STATUS_PUBLISHED) {
            return News::STATUS_PUBLISHED;
        }
        
        return News::STATUS_PENDING;
    }

    // faz upload da imagem e retorna o caminho
    private function handleImageUpload(Request $request): ?string
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('images', 'public');
        }
        
        return null;
    }

    // atualiza a imagem da noticia
    private function updateImage(Request $request, News $news): void
    {
        if ($request->hasFile('image')) {
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }
            $news->image_path = $request->file('image')->store('images', 'public');
        }
    }

    // atualiza campos basicos da noticia
    private function updateBasicFields(array $entry, News $news): void
    {
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
    }

    // atualiza status e destaque baseado nas permissoes do usuario
    private function updateStatusAndFeatured($user, array $entry, News $news): void
    {
        if ($user->isAdmin() || $user->isEditor()) {
            if (isset($entry['status'])) {
                $news->status = $entry['status'];
            }
            if (array_key_exists('is_featured', $entry)) {
                $news->is_featured = (bool) $entry['is_featured'];
            }
        } else {
            if ($news->status === News::STATUS_REJECTED) {
                $news->status = News::STATUS_PENDING;
                $news->rejection_reason = null;
            }
        }
    }

    // verifica se o usuario é admin ou editor
    private function isAdminOrEditor(): bool
    {
        $user = Auth::user();
        return $user->isAdmin() || $user->isEditor();
    }

    // garante que o usuario é admin ou editor, retorna erro se não for
    private function ensureAdminOrEditor(): void
    {
        if (!$this->isAdminOrEditor()) {
            abort(403, 'Não autorizado.');
        }
    }

    // valida e retorna um valor de per_page valido
    private function getValidPerPage($perPage, int $default = 15): int
    {
        $perPage = (int) ($perPage ?: $default);
        return ($perPage >= 1 && $perPage <= 50) ? $perPage : $default;
    }
}