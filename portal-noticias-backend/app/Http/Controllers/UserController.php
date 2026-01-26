<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    // lista todos os usuários
    public function index()
    {
        $this->ensureAdmin();

        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($users);
    }

    // cria um novo usuário
    public function store(UserStoreRequest $request)
    {
        $entry = $request->validated();

        $user = User::create([
            'name' => format_name($entry['name']),
            'email' => $entry['email'],
            'password' => Hash::make($entry['password']),
            'role' => $entry['role'],
        ]);

        return response()->json($user, 201);
    }

    // atualiza um usuário
    public function update(UserUpdateRequest $request, User $user)
    {
        $entry = $request->validated();

        if (isset($entry['name'])) {
            $user->name = format_name($entry['name']);
        }

        if (isset($entry['email'])) {
            $user->email = $entry['email'];
        }

        if (isset($entry['password']) && $entry['password']) {
            $user->password = Hash::make($entry['password']);
        }

        if (isset($entry['role'])) {
            $user->role = $entry['role'];
        }

        $user->save();

        return response()->json($user);
    }

    // deleta um usuário
    public function destroy(User $user)
    {
        $this->ensureAdmin();

        if ($user->id === auth()->user()->id) {
            return $this->errorResponse('Não é possível excluir o próprio usuário.', 400);
        }

        $user->delete();

        return response()->json(null, 204);
    }

    // exibe detalhes de um usuário
    public function show(User $user)
    {
        $this->ensureAdmin();

        return response()->json($user);
    }

    // atualiza o nome do usuário autenticado
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

    // verifica se o usuário é admin, retorna erro se não for
    private function ensureAdmin(): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Não autorizado.');
        }
    }
}