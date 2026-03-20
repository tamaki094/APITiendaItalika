<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
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
        // return response()->json([
        //     'message' => 'CHECKOUT NOT IMPLEMENTED YET'
        // ], 501);

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
                    'oder_id' => $existing->id,
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

    }

