<?php

namespace Modules\Role\Exceptions;

use App\Exceptions\BaseExceptionClass;
use Symfony\Component\HttpFoundation\Response;

class RoleException extends BaseExceptionClass
{
    /**
     * @throws RoleException
     */
    public static function alreadyExists()
    {
        throw new static(translate_error_message('role', 'exists'), Response::HTTP_BAD_REQUEST);
    }
}
