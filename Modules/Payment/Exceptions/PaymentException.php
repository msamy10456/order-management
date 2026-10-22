<?php

namespace Modules\Payment\Exceptions;

use App\Exceptions\BaseExceptionClass;
use App\Traits\HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class PaymentException extends BaseExceptionClass
{
    use HttpResponse;

    /**
     * @throws PaymentException
     */
    public static function paymentDeclined(): self
    {
        throw new self(
            translate_word('payment_declined'),
            Response::HTTP_PAYMENT_REQUIRED
        );
    }

    // GatewayResponse
    public static function unsupportedMethod(string $method): self
    {
        throw new self(
            translate_word('unsupported_payment_method', ['method' => $method]),
            Response::HTTP_BAD_REQUEST
        );
    }

    public static function invalidGateway(string $method): self
    {
        throw new self(
            translate_word('invalid_payment_gateway', ['method' => $method]),
            Response::HTTP_BAD_REQUEST
        );
    }
}
