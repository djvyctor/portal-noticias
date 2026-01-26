<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    // verifica se o usuario pode ver todas as noticias
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    // verifica se o usuario pode ver uma noticia especifica
    public function view(User $user, News $news): bool
    {
        return $this->isOwner($user, $news) || $this->isAdminOrEditor($user);
    }

    // verifica se o usuario pode criar noticias
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor() || $user->isJornalista();
    }

    // verifica se o usuario pode atualizar uma noticia
    public function update(User $user, News $news): bool
    {
        return $this->isOwner($user, $news) || $this->isAdminOrEditor($user);
    }

    // verifica se o usuario pode deletar uma noticia
    public function delete(User $user, News $news): bool
    {
        return $this->isOwner($user, $news) || $this->isAdminOrEditor($user);
    }

    // verifica se o usuario pode aprovar uma noticia
    public function approve(User $user, News $news): bool
    {
        return $this->isAdminOrEditor($user);
    }

    // verifica se o usuario pode rejeitar uma noticia
    public function reject(User $user, News $news): bool
    {
        return $this->isAdminOrEditor($user);
    }

    // verifica se o usuario é o dono da noticia
    private function isOwner(User $user, News $news): bool
    {
        return $user->id === $news->user_id;
    }

    // verifica se o usuario é admin ou editor
    private function isAdminOrEditor(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }
}
