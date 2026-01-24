<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode ver todos os usuários
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determina se o usuário pode ver um usuário específico
     */
    public function view(User $user, User $model)
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Determina se o usuário pode criar novos usuários
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determina se o usuário pode atualizar um usuário
     */
    public function update(User $user, User $model)
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Determina se o usuário pode deletar um usuário
     */
    public function delete(User $user, User $model)
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determina se o usuário pode criar editores
     */
    public function createEditor(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determina se o usuário pode criar jornalistas
     */
    public function createJornalista(User $user)
    {
        return $user->isAdmin();
    }
}