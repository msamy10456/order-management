<?php

namespace Modules\Payment\Services;

use App\Exceptions\ValidationErrorsException;
use Illuminate\Support\Facades\DB;
use Modules\Order\Enum\OrderStatusEnum;
use Modules\Order\Models\Order;
use Modules\Payment\Gateways\PaymentGatewayManager;
use Modules\Payment\Models\Payment;

class AdminPaymentService
{
    public function __construct(protected PaymentGatewayManager $gatewayManager = new PaymentGatewayManager()) {}

    public function index()
    {
        return Payment::query()
            ->searchable(['method'])
            ->filterByOrder()
            ->filterByStatus()
            ->adminDetails()
            ->paginatedCollection();
    }

    public function show($id)
    {
        return Payment::query()
            ->adminDetails()
            ->findOrFail($id);
    }


    /**
     * Process payment simulation for an order.
     *
     * @throws ValidationErrorsException
     */
    public function processPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $order = Order::with('items')->findOrFail($data['order_id']);
            if ((int) $order->status !== OrderStatusEnum::CONFIRMED) {
                throw new ValidationErrorsException([
                    'order' => 'Payments can only be processed for orders in confirmed status.'
                ]);
            }
            $gateway = $this->gatewayManager->getGateway($data['method']);

            $result = $gateway->process($order, $data);

            $amount = (float) $order->items()->sum('total');

            $payment = Payment::create([
                'order_id' => $order->id,
                'status' => $result['status'],
                'method' => $data['method'],
                'amount' => $amount,
                'transaction_id' => $result['transaction_id'] ?? null,
            ]);

            return $payment;
        });
    }

}
