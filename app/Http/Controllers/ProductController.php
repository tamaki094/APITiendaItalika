<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Respuestas:
     * - 200 OK { "data": [...] "meta": { "total": int, "page": int } }
     * - 200 OK: lista vacia
     * Futuro: paginacion, filtros, (q, min_price, max_price).
     */
    public function index(Request $request): JsonResponse {
        //Hoy: Contrato sin datos reales.
        return response()->json([
            'data' => [],
            'meta' => [
                'total' => 0,
                'page' => 1
            ]
        ]);
    }

    /**
     * GET /api/products/{id}
     * Respuestas:
     * - 200 OK:
     * - 404 Not Found
     * Futuro: consulta real a MySql.
     */
    public function show(int $id): JsonResponse {
        //Hoy : 404 por defecto hasta que tengamos BD.
        return response()->json([
            'message' => 'product not found'
        ], 404);
    }
}

