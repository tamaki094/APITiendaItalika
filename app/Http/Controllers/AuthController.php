<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * POST /api/auth/login
     * Body: { "email": string, "password": string }
     * Respuestas:
     *  - 200 { "token": "plain_text_token", "token_type": "Bearer" }
     *  - 422 validación
     *  - 401 credenciales inválidas
     */
    public function login(Request $request): JsonResponse{

        $data = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string','min:6'],
        ]);

        // Autenticación stateless (sin usar sesión ni guard web):
        /** @var \App\Models\User|null $user */
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales inválidas.'],
            ])->status(401);
        }

        // Emitir token personal de Sanctum
        $token = $user->createToken('postman')->plainTextToken;

        return response()->json([
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 200);
    }


   /**
     * POST /api/auth/logout
     * Revoca el token que se usó para esta petición.
     * Requiere Authorization: Bearer <token>
     */
    public function logout(Request $request): JsonResponse{

        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Sesión cerrada'], 200);
    }
}
