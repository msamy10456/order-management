<?php

namespace Modules\Payment\Gateways\Contracts;

use Modules\Order\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Process payment simulation for an order.
     */
    public function process(Order $order, array $data): array;

    /**
     * Get the gateway identifier name.
     */
    public function getIdentifier(): string;
}
