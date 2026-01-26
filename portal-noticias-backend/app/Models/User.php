<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // constantes para roles dos usuarios
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_JORNALISTA = 'jornalista';

    // campos que podem ser preenchidos em massa
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    // campos que nao devem aparecer em JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // converte tipos automaticamente
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // relacao, um usuario tem muitas noticias
    public function news()
    {
        return $this->hasMany(News::class);
    }

    // verifica se o usuario é admin
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // verifica se o usuario é editor
    public function isEditor(): bool
    {
        return $this->role === self::ROLE_EDITOR;
    }

    // verifica se o usuario é jornalista
    public function isJornalista(): bool
    {
        return $this->role === self::ROLE_JORNALISTA;
    }

    // verifica se o usuario pode publicar noticias direto
    public function canPublishDirectly(): bool
    {
        return $this->isAdmin() || $this->isEditor();
    }
}