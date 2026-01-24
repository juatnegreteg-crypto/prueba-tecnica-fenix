<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\BitfinexService;

class OrderController extends Controller
{
    protected $bitfinexService;

    public function __construct(BitfinexService $bitfinexService)
    {
        $this->bitfinexService = $bitfinexService;
    }

public function sync()
{
    try {
        $externalOrders = $this->bitfinexService->getOrdersHistory();

        // VALIDACIÓN CLAVE: Si el primer elemento es "error", detenerse y avisar
        if (isset($externalOrders[0]) && $externalOrders[0] === 'error') {
            return response()->json([
                'error' => 'Bitfinex dice: ' . ($externalOrders[2] ?? 'Error desconocido'),
                'detalles' => $externalOrders
            ], 401);
        }

        if (!is_array($externalOrders)) {
            return response()->json(['error' => 'Respuesta de API no válida'], 500);
        }

        $newCount = 0;
        $ignoredCount = 0;

        foreach ($externalOrders as $orderData) {
        
            if (!isset($orderData[0]) || !is_numeric($orderData[0])) continue;

            $bitfinexId = $orderData[0];
            $exists = Order::where('bitfinex_id', $bitfinexId)->exists();

            if (!$exists) {
                Order::create([
                    'bitfinex_id' => $bitfinexId,
                    'symbol'      => $orderData[3] ?? 'N/A',
                    'type'        => $orderData[8] ?? 'MARKET',
                    'amount'      => $orderData[6] ?? 0,
                    'price'       => $orderData[16] ?? 0,
                    'status'      => $orderData[13] ?? 'UNKNOWN',
                    'mts_create'  => isset($orderData[4]) ? date('Y-m-d H:i:s', intval($orderData[4]) / 1000) : null,
                ]);
                $newCount++;
            } else {
                $ignoredCount++;
            }
        }

        return response()->json([
            'ordenes_recibidas' => count($externalOrders),
            'ordenes_nuevas_guardadas' => $newCount,
            'ordenes_ignoradas' => $ignoredCount
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
    }
}

    public function index()
    {
        return response()->json(Order::all());
    }

    public function show($id)
    {
        $order = Order::where('bitfinex_id', $id)->first();;
        if (!$order) {
            return response()->json(['error' => 'Orden no encontrada.'], 404);
        }
        return response()->json($order);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Orden no encontrada.'], 404);
        }
        $order->delete();
        return response()->json(['message' => 'Orden eliminada correctamente.']);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Orden no encontrada.'], 404);
        }
        $order->update($request->only(['status','price','amount']));
        return response()->json(['message' => 'Orden actualizada correctamente.', 'order' => $order]);
    }
}
