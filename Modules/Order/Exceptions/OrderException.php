<?php

namespace Modules\Payment\Exceptions;

use App\Exceptions\BaseExceptionClass;
use App\Traits\HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderException extends BaseExceptionClass
{
    use HttpResponse;

    /**
     * @throws OrderException
     */
    public static function orderNotConfirmed(): self
    {
        throw new self(
            translate_word('order_not_confirmed'),
            Response::HTTP_BAD_REQUEST
        );
    }

    public static function orderAlreadyPaid(): self
    {
        throw new self(
            translate_word('order_already_paid'),
            Response::HTTP_BAD_REQUEST
        );
    }
}
