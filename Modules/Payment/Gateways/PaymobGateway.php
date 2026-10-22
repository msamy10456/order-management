<?php

namespace Modules\Payment\Gateways;

use Modules\Order\Models\Order;
use Modules\Payment\Enum\PaymentStatusEnum;
use Modules\Payment\Gateways\Contracts\PaymentGatewayInterface;

class PaymobGateway implements PaymentGatewayInterface
{
    public function __construct(protected array $config = []) {}

    public function process(Order $order, array $data): array
    {
        if (($data['simulate_failure'] ?? false) === true) {
            return [
                'success' => false,
                'status' => PaymentStatusEnum::FAILED,
                'transaction_id' => 'PAYMOB-FAIL-' . rand(1000000, 9999999),
                'message' => 'Paymob transaction was declined.',
                'details' => [
                    'gateway' => 'paymob',
                    'reason' => 'transaction_rejected',
                ],
            ];
        }

        return [
            'success' => true,
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'transaction_id' => 'PAYMOB-' . rand(1000000, 9999999),
            'message' => 'Paymob payment intention processed successfully.',
            'details' => [
                'gateway' => 'paymob',
                'integration_id' => $this->config['integration_id'] ?? 'mock_integration',
            ],
        ];
    }

    public function getIdentifier(): string
    {
        return 'paymob';
    }
}
