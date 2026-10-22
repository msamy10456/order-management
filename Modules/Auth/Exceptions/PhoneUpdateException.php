<?php

namespace Modules\Auth\Exceptions;

use App\Exceptions\BaseExceptionClass;
use Symfony\Component\HttpFoundation\Response;

class PhoneUpdateException extends BaseExceptionClass
{
    /**
     * @throws PhoneUpdateException
     */
    public static function notExists()
    {
        throw new self(
            translate_word('phone_update_request_not_sent'),
            Response::HTTP_BAD_REQUEST,
        );
    }

    /**
     * @throws PhoneUpdateException
     */
    public static function invalidCode()
    {
        throw new self(translate_word('invalid_verify_code'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @throws PhoneUpdateException
     */
    public static function expiredCode()
    {
        throw new self(translate_word('expired_verify_code'), Response::HTTP_BAD_REQUEST);
    }
}
