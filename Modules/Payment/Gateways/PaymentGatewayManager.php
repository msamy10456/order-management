<?php

namespace Modules\Payment\Gateways;

use InvalidArgumentException;
use Modules\Payment\Gateways\Contracts\PaymentGatewayInterface;

class PaymentGatewayManager
{
    /**
     * Resolve the payment gateway instance by method name.
     *
     * @throws InvalidArgumentException
     */
    public function getGateway(string $method): PaymentGatewayInterface
    {
        $gateways = config('payment.gateways', []);

        if (! isset($gateways[$method]['class'])) {
            throw new InvalidArgumentException("Payment gateway [{$method}] is not configured or supported.");
        }

        $gatewayConfig = $gateways[$method];
        $className = $gatewayConfig['class'];

        if (! class_exists($className)) {
            throw new InvalidArgumentException("Payment gateway class [{$className}] does not exist.");
        }

        return new $className($gatewayConfig);
    }

    /**
     * Get all supported gateway names.
     */
    public function availableGateways(): array
    {
        return array_keys(config('payment.gateways', []));
    }
}
