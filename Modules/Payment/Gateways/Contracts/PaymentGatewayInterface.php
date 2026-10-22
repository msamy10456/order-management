<?php

namespace Modules\Payment\Gateways\Contracts;

use Modules\Order\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Process payment simulation for an order.
     *
     * @param Order $order
     * @param array $data
     * @return array
     */
    public function process(Order $order, array $data): array;

    /**
     * Get the gateway identifier name.
     *
     * @return string
     */
    public function getIdentifier(): string;
}
