<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * se o usuário pode ver todos os usuários
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * se o usuario pode ver um usuário especifico
     */
    public function view(User $user, User $model)
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * se o usuário pode criar novos usuários
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * se o usuário pode atualizar um usuário
     */
    public function update(User $user, User $model)
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**se o usuário pode deletar um usuário
     */
    public function delete(User $user, User $model)
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * se o usuário pode criar editores
     */
    public function createEditor(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * se o usuário pode criar jornalistas
     */
    public function createJornalista(User $user)
    {
        return $user->isAdmin();
    }
}