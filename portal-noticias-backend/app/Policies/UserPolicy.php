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
    // Admin pode criar qualquer tipo, Editor pode criar apenas jornalistas
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    // verifica se o usuario pode atualizar um usuario
    // Admin pode atualizar qualquer usuário, Editor pode atualizar mas não pode alterar role
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->isEditor() || $user->id === $model->id;
    }

    // verifica se o usuario pode deletar um usuario
    // Apenas Admin pode deletar usuários (Editor não pode)
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
    // Admin e Editor podem criar jornalistas
    public function createJornalista(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }
}