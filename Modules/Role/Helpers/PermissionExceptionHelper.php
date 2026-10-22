<?php

namespace Modules\Role\Helpers;

use App\Helpers\BaseExceptionHelper;
use Illuminate\Foundation\Configuration\Exceptions;
use Modules\Role\Exceptions\RoleException;
use Modules\Role\Exceptions\UserPermissionException;

class PermissionExceptionHelper extends BaseExceptionHelper
{
    public static function handle(Exceptions $exceptions): void
    {
        $exceptions->render(fn (UserPermissionException $e) => self::generalErrorResponse($e));
        $exceptions->render(fn (RoleException $e) => self::generalErrorResponse($e));
    }
}
