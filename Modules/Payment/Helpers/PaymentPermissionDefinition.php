<?php

namespace Modules\Payment\Helpers;

use Modules\Role\Abstracts\PermissionDefinition;

class PaymentPermissionDefinition extends PermissionDefinition
{
    public static function permissions(): array
    {
        return [
            'payment' => self::generatePermissionsArray(),
        ];
    }
}
