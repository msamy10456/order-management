<?php

namespace Modules\Role\Helpers;

use Modules\Role\Models\Permission;
use Modules\Role\Models\Role;

class PermissionHelper
{
    public static function generatePermissionName(string $operation, string $role): string
    {
        return $operation.'-'.$role;
    }

    public static function getPermissionNameMiddleware(string $operation, string $role): string
    {
        return 'permission:'.self::generatePermissionName($operation, $role);
    }

    public static function roleModel(): Role
    {
        return new Role;
    }

    public static function permissionModel(): Permission
    {
        return new Permission;
    }
}
