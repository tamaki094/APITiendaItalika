<?php

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


// --- Autenticación (tokens personales) -
Route::prefix('auth')->group(function (){
    Route::post('/login', [AuthController::class, 'login'])
        ->name('auth.login');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('auth.logout');
});

// --- Catálogo(publico por ahora)
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');// 200 lista, 200 vacía
Route::get('/products/{id}', [ProductController::class, 'show'])
    ->whereNumber('id')
    ->name('products.show'); // 200 detalle, 404 no encontrado


// --- Checkout (protegido)
Route::post('/orders', [OrderController::class, 'store'])
    ->middleware('auth:sanctum') //requiere token válido
    ->name('orders.store');
