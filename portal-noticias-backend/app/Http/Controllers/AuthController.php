<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // tempo de expiração do token de reset de senha (em minutos)
    private const PASSWORD_RESET_EXPIRY_MINUTES = 60;

    // registro de usuário e cria um token
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => format_name($request->name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'jornalista', // medida de segurança - MASS ASSIGNMENT
        ]);

        return $this->createAuthResponse($user, 201);
    }

    // autentica usuário e retorna token
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.']
            ]);
        }

        return $this->createAuthResponse($user);
    }

    // faz logout do usuário autenticado
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }

    // retorna dados do usuário autenticado
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // gera token para reset de senha
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $token = \Illuminate\Support\Str::random(64);
        
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $resetUrl = "{$frontendUrl}/resetar-senha?token={$token}&email=" . urlencode($request->email);

        return response()->json([
            'message' => 'Token de recuperação gerado. Em produção será enviado por email.',
            'token' => $token,
            'reset_url' => $resetUrl
        ]);
    }

    // redefine senha usando token
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return response()->json([
                'message' => 'Token inválido ou expirado.'
            ], 400);
        }

        if (now()->diffInMinutes($reset->created_at) > self::PASSWORD_RESET_EXPIRY_MINUTES) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'message' => 'Token expirado. Solicite um novo.'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Senha alterada com sucesso!'
        ]);
    }

    // altera senha do usuário autenticado
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Senha atual incorreta.'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Senha alterada com sucesso!'
        ]);
    }

    // cria resposta de autenticação padronizada
    private function createAuthResponse(User $user, int $statusCode = 200)
    {
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], $statusCode);
    }
}