<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    /**
     * POST /api/orders
     * Body:
     * {
     *   "currency": "MXN",
     *   "items": [
     *     { "product_id": 1, "quantity": 2 },
     *     { "product_id": 5, "quantity": 1 }
     *   ]
     * }
     */
    public function store(Request $request): JsonResponse{
        $user = $request->user();

        //Idempotency
        $idempotencyKey = $request->header('Idempotency-Key');
        if($idempotencyKey){
            $existing = Order::where('user_id', $user->id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if($existing){
                return response()->json([
                    'message' => 'Orden ya existe para esta clave de idempotencia.',
                    'order_id' => $existing->id,
                ],409);
            }
        }

        $payload = $request->validated();
        $currency = strtoupper($payload['currency'] ?? 'MXN');
        $itemsReq = $payload['items'];

        // Transaccion atomica
        $order = DB::transaction(function () use ($user, $currency, $itemsReq, $idempotencyKey) {
            $subtotal = 0;
            $tax = 0;
            $total = 0;

            //Calcula montos y verifica stock
            $lineItems = [];
            foreach($itemsReq as $row){
                /** @var Product $product */
                $product = Product::lockforUpdate()->find($row['product_id']);

                if(!$product){
                    abort(422, "Producto {$row['product_id']} no encontrado.");
                }

                $qty = (int) $row['quantity'];
                if($product->stock < $qty){
                    abort(409, "Stock insuficiente para producto {$product->id} ({$product->name}).");
              }

              $unit = (float) $product->price;
              $lineTotal = $unit * $qty;

              $subtotal += $lineTotal;

              $lineItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'line_total' => $lineTotal
              ];
            }

            //Aplicando impuestos
            $total = $subtotal + $tax;

            //Crea la orden
            /** @var Order $order */
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'currency' => $currency,
                'idempotency_key' => $idempotencyKey

            ]);

            //Inserta items  y descuenta inventaruio

            foreach($lineItems as $line){
                OrderItem::create([
                    'oder_id' => $order->id,
                    'product_id' => $line['product_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['line_total']
                ]);

                Product::where('id', $line['product_id'])
                    ->decrement('stock', $line['quantity']);
            }

            return $order->load(['items.product']);
        });

        return response()->json([
            'message' => 'Orden creada exitosamente.',
            'order' => $order
        ], 201);
    }

    /**
     * GET /api/orders/{id}
     * Muestra la orden si pertenece al usuario. Incluye items + product.
     */
    public function show(Request $request, int $id): JsonResponse{
        $user = $request->user();

        $order = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->find($id);

        if(!$order){
            return response()->json([
                'message' => 'Orden no encontrada.'
            ], 404);
        }

        return response()->json($order, 200);
    }

    /**
     * POST /api/orders/{id}/pay
     * Marca la orden como pagada si es del usuario esta en estado pendiente. (Simula pago exitoso)
     */
    public function pay(Request $request, int $id): JsonResponse{
        $user = $request->user();

        $order =Order::where('user_id', $user->id)
            ->find($id);

        if(!$order){
            return response()->json([
                'message' => 'Orden no encontrada.'
            ],404);
        }

        if($order->status === 'paid'){
            return response()->json([
                'message' => 'La orden ya esta pagada'
            ], 409);
        }

        if($order->status !== 'pending'){
            return response()->json([
                'message'=> "La orden no se puede pagar en estado '{order->status}'"
            ], 409);
        }

        $order->update([
            'status' => 'paid'
        ]);

        return response()->json([
            'message' => 'Orden marcada como pagada exitosamente.',
            'order' => $order->fresh()
        ], 200);
    }

    /**
     * GET /api/orders/stats
     * Panel con KPIs del usuario autenticado.
     */
    public function stats(Request $request): JsonResponse{
        $user = $request->user();

        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalSpent = Order::where('user_id', $user->id)->where('status', 'paid')
            ->sum('total');

        $lastOrders = Order::withCount('items')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'status', 'total', 'created_at']);

        return response()->json([
            'kpis' => [
                'orders_count' => (int)$totalOrders,
                'total_spent' => (float)$totalSpent,
            ],
            'last_orders' => $lastOrders
        ], 200);

    }

    /**
     * POST /api/orders/{id}/cancel
     * Cancela la orden si esta en estado pendiente. (Simula cancelacion exitosa)
     */
    public function cancel(Request $request, int $id) : JsonResponse{
        $user = $request->user();

        //Cargar orden del usuario
        $order = Order::with('items')
            ->where('user_id', $user->id)
            ->find($id);

        if(!$order){
            return response()->json([
                'message' => 'Orden no encontrada.'
            ], 404);
        }

        if($order->status !== 'pending'){
            return response()->json([
                'message' => "La orden no se puede cancelar en estado '{$order->status}'"
            ], 409);
        }

        DB::transaction(function () use ($order, $user){

            //Devolviendo stock item por item
            foreach($order->items as $item){
                Product::where('id', $item->product_id)
                    ->increment('stock', $item->quantity);
            }

            //Cambiar estado
            $old = $order->status;
            $order->update(['status' => 'cancelled']);

            //Registrar historial de cambios de estado
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $old,
                'new_status' => 'cancelled',
                'changed_by_user_id' => $user->id,
                'note' => 'Orden cancelada por el usuario.'
            ]);
        });

        return response()->json([
            'message' => 'Orden cancelada exitosamente.',
            'order' => $order->fresh()
        ], 200);
    }

    public function paymentWebhook(Request $request): JsonResponse{
        $data = $request->validate([
            'order_id' => ['required', 'integer'],
            'status' => ['required', 'string']
        ]);

        /** @var Order|null $order */
        $order = Order::find($data['order_id']);

        if(!$order){
            return response()->json([
                'message' => 'Orden no encontrada.'
            ], 202);
        }

        //Solo se procesa si el estado es "paid" (simulando que el webhook solo notifica pagos exitosos)
        if($order->status !== 'paid'){
            return response()->json([
                'message' => "Estado de  pago no soportado: {$data['status']}."
            ], 202);
        }

        //Si la orden ya esta marcada como pagada, no se hace nada (idempotencia)
        if($order->status === 'paid'){
            return response()->json([
                'message' => 'La orden ya esta marcada como pagada.'
            ], 200);
        }

        //No se puede pagar si esta en otro estado
        if($order->status !== 'pending'){
            return response()->json([
                'message' => "La orden no se puede marcar como pagada en estado '{$order->status}'."
            ], 409);
        }

        DB::transaction(function () use ($order){
            $old = $order->status;
            $order->update(['status' => 'paid']);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $old,
                'new_status' => 'paid',
                'changed_by_user_id' => null,
                'note' => 'Orden marcada como pagada por webhook de pago.'
            ]);
        });

        return response()->json([
            'message' => 'Webhook procesado, orden marcada como pagada.',
            'order' => $order->fresh()
        ], 200);
    }
}




