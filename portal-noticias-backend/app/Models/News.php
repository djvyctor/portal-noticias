<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class News extends Model
{
    // constantes para status das noticias
    public const STATUS_PENDING = 'pending';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';

    // constantes para paginação
    public const PAGINATION_DEFAULT = 10;
    public const PAGINATION_ADMIN = 15;
    public const CAROUSEL_LIMIT = 5;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image_path',
        'status',
        'is_featured',
        'user_id',
        'category_id',
        'published_at',
        'author_name',
        'rejection_reason',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    // relacao, uma noticia pertence a um usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relacao, uma noticia pertence a uma categoria
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // verifica se a noticia esta publicada
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    // verifica se a noticia esta pendente
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    // verifica se a noticia foi rejeitada
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // scope para noticias publicadas
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    // scope para noticias pendentes
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // scope para noticias rejeitadas
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // scope para noticias em destaque
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    // scope para noticias comuns
    public function scopeNotFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', false);
    }

    // scope para eager loading padrao de relacoes
    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['user:id,name', 'category:id,name,slug']);
    }
}
