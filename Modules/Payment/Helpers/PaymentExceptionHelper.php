<?php

namespace Modules\Payment\Helpers;

use App\Helpers\BaseExceptionHelper;
use Illuminate\Foundation\Configuration\Exceptions;
use Modules\Payment\Exceptions\PaymentException;

class PaymentExceptionHelper extends BaseExceptionHelper
{
    public static function handle(Exceptions $exceptions)
    {
        $exceptions->renderable(fn (PaymentException $e) => self::generalErrorResponse($e));
    }
}
