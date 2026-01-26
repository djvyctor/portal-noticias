<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    // verifica se o usuario pode ver todos os usuarios
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    // verifica se o usuario pode ver um usuario especifico
    // admin pode ver qualquer um e o usuario pode ver a si mesmo
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    // verifica se o usuario pode criar novos usuarios
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    // verifica se o usuario pode atualizar um usuario
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    // verifica se o usuario pode deletar um usuario
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    // verifica se o usuario pode criar editores
    public function createEditor(User $user): bool
    {
        return $user->isAdmin();
    }

    // verifica se o usuario pode criar jornalistas
    public function createJornalista(User $user): bool
    {
        return $user->isAdmin();
    }
}