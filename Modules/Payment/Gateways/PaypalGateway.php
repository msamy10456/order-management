<?php

namespace Modules\Payment\Gateways;

use Modules\Order\Models\Order;
use Modules\Payment\Enum\PaymentStatusEnum;
use Modules\Payment\Gateways\Contracts\PaymentGatewayInterface;

class PaypalGateway implements PaymentGatewayInterface
{
    public function __construct(protected array $config = []) {}

    public function process(Order $order, array $data): array
    {
        if (($data['simulate_failure'] ?? false) === true) {
            return [
                'success' => false,
                'status' => PaymentStatusEnum::FAILED,
                'transaction_id' => 'PAYID-FAIL-' . uniqid(),
                'message' => 'PayPal payment was rejected by customer or cancelled.',
                'details' => [
                    'gateway' => 'paypal',
                    'reason' => 'buyer_cancelled',
                ],
            ];
        }

        return [
            'success' => true,
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'transaction_id' => 'PAYID-' . strtoupper(uniqid()),
            'message' => 'PayPal payment approved and captured.',
            'details' => [
                'gateway' => 'paypal',
                'payer_email' => $data['payer_email'] ?? $order->customer_email ?? 'payer@paypal-sandbox.com',
                'mode' => $this->config['sandbox'] ?? true ? 'sandbox' : 'live',
            ],
        ];
    }

    public function getIdentifier(): string
    {
        return 'paypal';
    }
}
