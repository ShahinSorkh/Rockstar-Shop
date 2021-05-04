<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function indexOrders(Request $request)
    {
        return OrderResource::collection(
            $this->orderService->indexOrders($request->user())
        );
    }

    public function placeOrder(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|numeric|exists:products,id',
            'customizations' => 'array',
            'customizations.*.product_customization_id' => 'numeric|exists:product_customizations,id',
        ]);
        // FIXME check customization option exists in product customization
        // options as validation constraint

        return new OrderResource($this->orderService->placeOrder(
            $request->user(),
            $request->product_id,
            $request->customizations,
        ));
    }

    public function cancelOrder(Request $request, Order $order)
    {
        if ($request->user()->id !== $order->user->id)
            abort(403);
        $this->orderService->cancelOrder($order);
    }

    public function updateOrder(Request $request, Order $order)
    {
        $this->validate($request, [
            'product_id' => 'required|numeric|exists:products,id',
            'customizations' => 'array',
            'customizations.*.product_customization_id' => 'numeric|exists:product_customizations,id',
        ]);
        // FIXME check customization option exists in product customization
        // options as validation constraint

        if ($request->user()->id !== $order->user->id)
            abort(403);
        $this->orderService->updateOrder($order, $request->product_id, $request->customizations);

        return new OrderResource($order);
    }
}
