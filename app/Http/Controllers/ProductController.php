<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{

/**
     * GET /api/products
     * Query params opcionales:
     * - q:        string (búsqueda por nombre)
     * - min_price float
     * - max_price float
     * - sortBy:   id|name|price (default: id)
     * - sortDir:  asc|desc      (default: asc)
     * - per_page: 1..100        (default: 10)
     * - page:     >=1           (controlado automáticamente por Laravel)
     */
    public function index(Request $request): JsonResponse {
        $query = Product::query();

        // --- Búsqueda por nombre ---
        if ($q = $request->query('q')) {
            $query->where('name', 'LIKE', '%' . $q . '%');
        }

        // --- Filtros de precio ---
        if ($min = $request->query('min_price')) {
            $query->where('price', '>=', (float) $min);
        }
        if ($max = $request->query('max_price')) {
            $query->where('price', '<=', (float) $max);
        }

        // --- Ordenamiento ---
        $sortBy = $request->query('sortBy', 'id');
        $sortDir = strtolower($request->query('sortDir', 'asc')) === 'desc' ? 'desc' : 'asc';
        if (! in_array($sortBy, ['id', 'name', 'price'], true)) {
            $sortBy = 'id';
        }
        $query->orderBy($sortBy, $sortDir);

        // --- Paginación ---
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 10;

        $results = $query->paginate($perPage);

        return response()->json([
            'data' => $results->items(),
            'meta' => [
                'total'        => $results->total(),
                'per_page'     => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page'    => $results->lastPage(),
                'from'         => $results->firstItem(),
                'to'           => $results->lastItem(),
                'sortBy'       => $sortBy,
                'sortDir'      => $sortDir,
                'filters'      => [
                    'q'         => $q ?? null,
                    'min_price' => $min ?? null,
                    'max_price' => $max ?? null,
                ],
            ],
        ], 200);
    }

    /**
     * GET /api/products/{id}
     * Respuestas:
     * - 200 OK:
     * - 404 Not Found
     * Futuro: consulta real a MySql.
     */
    public function show(int $id): JsonResponse {
        $product = Product::find($id);
        if(!$product){
            return response()->json(['message' => 'product not found'], 404);
        }

        return response()->json($product, 200);
    }
}

