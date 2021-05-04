<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function indexOrders(User $user)
    {
        return $user->orders;
    }

    public function placeOrder(User $user, int $product_id, ?array $customizations)
    {
        $product = Product::find($product_id);
        $order = $user->orders()->create([
            'product_id' => $product->id,
            'price' => $product->price,
            'status' => Order::STATUS_WAITING,
        ]);
        if ($customizations !== null && !empty($customizations))
            $order->customizations()->createMany($customizations);
        return $order;
    }

    public function cancelOrder(Order $order)
    {

        if ($order->status !== Order::STATUS_WAITING)
            abort(403);
        $order->delete();
    }

    public function updateOrder(Order $order, int $product_id, ?array $customizations)
    {
        if ($order->status !== Order::STATUS_WAITING)
            abort(403);
        DB::transaction(function () use ($order, $product_id, $customizations) {
            $order->customizations()->delete();
            $order->update(['product_id' => $product_id]);
            if ($customizations !== null && !empty($customizations))
                $order->customizations()->createMany($customizations);
        });
    }
}
