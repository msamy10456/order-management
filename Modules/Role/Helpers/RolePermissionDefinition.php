<?php

namespace Modules\Role\Helpers;

use Modules\Role\Abstracts\PermissionDefinition;

class RolePermissionDefinition extends PermissionDefinition
{
    public static function permissions(): array
    {
        return [
            'role' => static::generatePermissionsArray(),
        ];
    }
}
