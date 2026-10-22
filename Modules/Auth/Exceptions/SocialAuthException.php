<?php

namespace Modules\Auth\Exceptions;

use App\Exceptions\BaseExceptionClass;
use Symfony\Component\HttpFoundation\Response;

class SocialAuthException extends BaseExceptionClass
{
    /**
     * @throws SocialAuthException
     */
    public static function invalidCredentials()
    {
        throw new self(translate_word('invalid_social_credentials'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @throws SocialAuthException
     */
    public static function invalidEmail()
    {
        throw new self(translate_word('invalid_email'), Response::HTTP_BAD_REQUEST);
    }
}
