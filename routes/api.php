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
*/

// --- Salud del servicio ---
Route::get('/health', [HealthController::class, 'status'])
    ->name('health.status');

// --- Autenticación (tokens personales) ---
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->name('auth.login');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('auth.logout');
});

// --- Catálogo (público por ahora) ---
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index'); // 200 lista, 200 vacía

Route::get('/products/{id}', [ProductController::class, 'show'])
    ->whereNumber('id')
    ->name('products.show'); // 200 detalle, 404 no encontrado

// // --- Checkout (protegido) ---
// Route::post('/orders', [OrderController::class, 'store'])
//     ->middleware('auth:sanctum') // requiere token válido
//     ->name('orders.store');

Route::middleware('auth:sanctum')->group(function (){
    //Listado de ordenes de usuario
    Route::get('/orders', [OrderController::class, 'index'])
    ->name('orders.index');

    //Detalle de orden
    Route::get('/orders/{id}', [OrderController::class, 'show'])
        ->whereNumber('id')
        ->name('orders.show');

    //Marcar como pagada
    Route::post('/orders/{id}/pay', [OrderController::class, 'pay'])
        ->whereNumber('id')
        ->name('orders.pay');

    //Panel/estadisticas
    Route::get('/orders/stats', [OrderController::class, 'stats'])->name('orders.stats');

    //Cancelar orden (si esta pendiente)
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])
        ->whereNumber('id')
        ->middleware('throttle:cancel-orders')
        ->name('orders.cancel');

    //Webhook simulado de pago(en real vendria sin auth y validado por firma o token secreto)
    Route::post('/webhooks/payments', [OrderController::class, 'paymentWebhook'])
        ->middleware('throttle:payment-webhooks')
        ->name('webhooks.payments');



});
