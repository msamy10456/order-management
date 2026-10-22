<?php

namespace Modules\Order\Services;

use App\Exceptions\ValidationErrorsException;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\Order;

class OrderService
{
    public function index()
    {
        return Order::query()
            ->searchable(['customer_name'])
            ->filterByStatus()
            ->indexDetails()
            ->paginatedCollection();
    }

    public function show($id)
    {
        return Order::query()
            ->showDetails()
            ->findOrFail($id);
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = auth()->id();
            $order = Order::create($data);
            if (! empty($data['items'])) {
                OrderItemService::store($order, $data['items']);
            }

            return $order;
        });
    }

    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $order = Order::findOrFail($id);
            $order->update($data);
            if (isset($data['items']) && is_array($data['items'])) {
                $order->items()->delete();
                OrderItemService::store($order, $data['items']);
            }

            return $order;
        });
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        if ($order->payments()->exists()) {
            throw new ValidationErrorsException([
                'order' => __('messages.cannot_delete_order_with_payments') ?: 'Cannot delete an order that has associated payments.'
            ]);
        }

        $order->delete();
    }
}
