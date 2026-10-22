<?php

namespace Modules\Order\Helpers;

use Modules\Role\Abstracts\PermissionDefinition;

class OrderPermissionDefinition extends PermissionDefinition
{
    public static function permissions(): array
    {
        return [
            'order' => self::generatePermissionsArray(),
        ];
    }
}
