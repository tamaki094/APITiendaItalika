<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HealthController;





/*
|--------------------------------------------------------------------------
| API Routes (Contratos)
|--------------------------------------------------------------------------
| TODO (próximos bloques):
| - Proteger endpoints con auth:sanctum
| - Rate limiting específico por ruta
| - Validaciones con FormRequest
|
| Nota: Mantén este archivo como “fuente de verdad” del contrato:
|       endpoints, métodos, códigos de estado y errores esperados.
*/


// --- Salud del servicio ---
Route::get('/health', [HealthController::class, 'status'])
    ->name('health.status');


// --- Autenticación (Sanctum en Bloque 2) -
Route::prefix('auth')->group(function (){
    //POST /auth/login -> devuelve token (Bloque 2). Hoy: 501.
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // POST /auth/logout -> invalida token (Bloque 2). Hoy: 501.
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// --- Catálogo de productos (lectura pública por ahora) ---
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');// 200 lista, 200 vacía
Route::get('/products/{id}', [ProductController::class, 'show'])
    ->whereNumber('id')
    ->name('products.show'); // 200 detalle, 404 no encontrado


// --- Checkout / Órdenes (se protegerá con auth:sanctum) ---
Route::post('/orders', [OrderController::class, 'store'])
    ->name('orders.store'); // 201 creada, 400 datos inválidos, 401 no auth
