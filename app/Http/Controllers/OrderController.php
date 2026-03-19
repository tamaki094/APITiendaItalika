<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderController extends Controller
{
    /**
     * POST /api/orders
     * Headers esperados FUTURO: Idempotency-Key: <uuid>
     * Body esperado FUTURO: { "items": [ { "product_id": int, "quantity": int } ], "payment_method": "mock" }
     * Respuestas:
     * - 201 Created (Futuro)
     * - 400/422 Datos inválidos (Futuro)
     * - 409 Conflicto de idempotencia (Futuro)
     * Hoy: 501 Not Implemented.
     */
    public function store(Request $request): JsonResponse{
        return response()->json([
            'message' => 'CHECKOUT NOT IMPLEMENTED YET'
        ], 501);
    }
}
