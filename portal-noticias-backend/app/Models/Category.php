<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    // campos que podem ser preenchidos em massa
    protected $fillable = ['name', 'slug'];

    // relacao: uma categoria tem muitas noticias
    public function news()
    {
        return $this->hasMany(News::class);
    }

    // scope para ordenar categorias por nome
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }
}
