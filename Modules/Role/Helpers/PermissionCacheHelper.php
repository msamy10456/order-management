<?php

namespace Modules\Role\Helpers;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PermissionCacheHelper
{
    public static function setCachedPermissions(): void
    {
        cache()->forever('permissions', PermissionHelper::permissionModel()::all());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getCachedPermissions(): array
    {
        if (! cache()->has('permissions')) {
            self::setCachedPermissions();
        }

        return cache()->get('permissions');
    }
}
