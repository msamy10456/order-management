<?php

namespace Modules\Role\Abstracts;

abstract class PermissionDefinition
{
    abstract public static function permissions(): array;

    protected static function generatePermissionsArray(array $excludedOperations = [], bool $excludeAll = false, array $additionalPermissions = []): array
    {
        if ($excludeAll) {
            return [];
        }

        $availableOperations = array_merge(['all', 'show', 'store', 'update', 'delete'], $additionalPermissions);

        return array_diff($availableOperations, $excludedOperations);
    }
}
