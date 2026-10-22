<?php

namespace Modules\Payment\Gateways;

use Modules\Order\Models\Order;
use Modules\Payment\Enum\PaymentStatusEnum;
use Modules\Payment\Gateways\Contracts\PaymentGatewayInterface;

class CreditCardGateway implements PaymentGatewayInterface
{
    public function __construct(protected array $config = []) {}

    public function process(Order $order, array $data): array
    {
        $cardNumber = $data['card_number'] ?? ($data['card'] ?? null);

        // Simulation logic: simulate failure if requested or explicitly invalid test card
        if ($cardNumber === '0000000000000000' || ($data['simulate_failure'] ?? false) === true) {
            return [
                'success' => false,
                'status' => PaymentStatusEnum::FAILED,
                'transaction_id' => 'CC-FAIL-'.uniqid(),
                'message' => 'Credit card payment was declined.',
                'details' => [
                    'gateway' => 'credit_card',
                    'reason' => 'insufficient_funds_or_card_declined',
                ],
            ];
        }

        return [
            'success' => true,
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'transaction_id' => 'CC-TXN-'.strtoupper(uniqid()),
            'message' => 'Credit card payment processed successfully.',
            'details' => [
                'gateway' => 'credit_card',
                'last4' => $cardNumber ? substr((string) $cardNumber, -4) : '4242',
                'brand' => 'Visa',
            ],
        ];
    }

    public function getIdentifier(): string
    {
        return 'credit_card';
    }
}
