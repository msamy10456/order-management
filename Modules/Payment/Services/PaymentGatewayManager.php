<?php

namespace App\Services\Payments;

use Illuminate\Contracts\Container\Container;
use Modules\Payment\Exceptions\PaymentException;
use Modules\Payment\Services\Contract\PaymentGatewayInterface;

class PaymentGatewayManager
{
    public function __construct(private readonly Container $container) {}

    public function checkGateway(string $method): PaymentGatewayInterface
    {
        $class = config("payment.gateways.{$method}");

        if (! $class) {
            return PaymentException::unsupportedMethod($method);
        }

        $gateway = $this->container->make($class);

        if (! $gateway instanceof PaymentGatewayInterface) {
            return PaymentException::invalidGateway($method);
        }

        return $gateway;
    }

    /** @return list<string> */
    public function supported(): array
    {
        return array_keys(config('payment.gateways', []));
    }
}
