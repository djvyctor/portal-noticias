<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    // lista todos os usuarios
    // Admin vê todos, Editor também pode ver para gerenciar jornalistas
    public function index()
    {
        $currentUser = auth()->user();
        
        // Apenas Admin e Editor podem listar usuários
        if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
            abort(403, 'Não autorizado.');
        }

        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($users);
    }

    // cria um novo usuario
    // Editor só pode criar jornalistas, Admin pode criar qualquer tipo
    public function store(UserStoreRequest $request)
    {
        $entry = $request->validated();
        $currentUser = auth()->user();

        // Valida que Editor só pode criar jornalistas
        if ($currentUser->isEditor() && !$currentUser->isAdmin()) {
            if ($entry['role'] !== User::ROLE_JORNALISTA) {
                return $this->errorResponse('Editores só podem criar jornalistas.', 403);
            }
        }

        $user = User::create([
            'name' => format_name($entry['name']),
            'email' => $entry['email'],
            'password' => Hash::make($entry['password']),
            'role' => $entry['role'],
        ]);

        return response()->json($user, 201);
    }

    // atualiza um usuario
    // Editor não pode alterar role de usuários (apenas Admin pode)
    public function update(UserUpdateRequest $request, User $user)
    {
        $this->authorize('update', $user);
        
        $entry = $request->validated();
        $currentUser = auth()->user();

        if (isset($entry['name'])) {
            $user->name = format_name($entry['name']);
        }

        if (isset($entry['email'])) {
            $user->email = $entry['email'];
        }

        // Validação de senha: Editor só pode alterar a própria senha, Admin pode alterar qualquer senha
        if (isset($entry['password']) && $entry['password']) {
            // Editor não pode alterar senha de outros usuários
            if ($currentUser->isEditor() && !$currentUser->isAdmin() && $currentUser->id !== $user->id) {
                return $this->errorResponse('Editores não podem alterar a senha de outros usuários.', 403);
            }
            $user->password = Hash::make($entry['password']);
        }

        // Apenas Admin pode alterar role de usuários
        if (isset($entry['role'])) {
            if ($currentUser->isEditor() && !$currentUser->isAdmin()) {
                return $this->errorResponse('Editores não podem alterar a função de usuários.', 403);
            }
            $user->role = $entry['role'];
        }

        $user->save();

        return response()->json($user);
    }

    // deleta um usuario
    public function destroy(User $user)
    {
        $this->ensureAdmin();

        if ($user->id === auth()->user()->id) {
            return $this->errorResponse('Não é possível excluir o próprio usuário.', 400);
        }

        $user->delete();

        return response()->json(null, 204);
    }

    // exibe detalhes de um usuario
    // Admin e Editor podem ver detalhes
    public function show(User $user)
    {
        $currentUser = auth()->user();
        
        // Apenas Admin e Editor podem ver detalhes de usuários
        if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
            abort(403, 'Não autorizado.');
        }

        return response()->json($user);
    }

    // atualiza o nome do usuario autenticado
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->name = format_name($request->name);
        $user->save();

        return response()->json($user);
    }

    // verifica se o usuario é admin, retorna erro se não for
    private function ensureAdmin(): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Não autorizado.');
        }
    }
}