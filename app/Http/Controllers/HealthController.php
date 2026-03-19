<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthController extends Controller
{
    /**
     * GET /api/health
     * @return JsonResponse 200 { "status": "ok", "service":"tienda-italika", "version": "0.1.0" }
     */
    public function status(): JsonResponse {
        return response()->json([
            'status' => 'ok',
            'service' => 'tienda-italika',
            'version' => '0.1.0'
        ]);
    }
}

