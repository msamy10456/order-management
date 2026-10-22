<?php

namespace Modules\Order\Services;

use Modules\Order\Models\Order;

class OrderItemService
{
    public static function store(Order $order, array $items)
    {
        $rows = collect($items)->map(function ($item) {
            $quantity = $item['quantity'] ?? 1;
            $price = $item['price'] ?? 0;

            return [
                'product_name' => $item['product_name'],
                'quantity' => $quantity,
                'price' => $price,
                'total' => $quantity * $price,
            ];
        })->all();

        $order->items()->createMany($rows);
    }
}
