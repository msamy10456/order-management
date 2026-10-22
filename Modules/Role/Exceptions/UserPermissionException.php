<?php

namespace Modules\Role\Exceptions;

use App\Exceptions\BaseExceptionClass;
use Symfony\Component\HttpFoundation\Response;

class UserPermissionException extends BaseExceptionClass
{
    /**
     * @throws UserPermissionException
     */
    public static function notExists()
    {
        throw new static(translate_word('have_no_permission'), Response::HTTP_FORBIDDEN);
    }
}
