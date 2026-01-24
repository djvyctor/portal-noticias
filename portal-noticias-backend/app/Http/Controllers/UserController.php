<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,editor,jornalista',
        ]);
        $name = ucwords(strtolower(trim($request->name)));

        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|null|string|min:8',
            'role' => 'sometimes|in:admin,editor,jornalista',
        ]);

        if ($request->has('name')) {
            $user->name = ucwords(strtolower(trim($request->name)));
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password') && $request->password) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('role')) {
            $user->role = $request->role;
        }

        $user->save();

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }
        if ($user->id === auth()->user()->id) {
            return response()->json(['message' => 'Não é possível excluir o próprio usuário.'], 400);
        }

        $user->delete();

        return response()->json(null, 204);
    }

    public function show(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        return response()->json($user);
    }

    public function updateName(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $user->name = ucwords(strtolower(trim($request->name)));
        $user->save();

        return response()->json($user);
    }
}