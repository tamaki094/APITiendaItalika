<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     * Body: { "email": string, "password": string }
     * FUTURO (Bloque 2): Validar credenciales, emitir token Sanctum.
     * Hoy: 501 Not Implemented.
     */
    public function login(Request $request): JsonResponse{
        return response()->json([
            'message' => 'login not implemented yet'
        ], 501);
    }

    /**
     * POST /api/auth/logout
     * FUTURO (Bloque 2): revocar token actual.
     * Hoy: 501 Not Implemented.
     */
    public function logout(Request $request): JsonResponse{
        return response()->json([
            'message' => 'logout not implemented yet'
        ], 501);
    }
}
