<?php

namespace Modules\Role\Services;

use App\Exceptions\ValidationErrorsException;
use Modules\Role\Models\Permission;

class PermissionService
{
    /**
     * @throws ValidationErrorsException
     */
    public function exist($permissions)
    {
        $existingPermissions = Permission::query()
            ->whereIntegerInRaw('id', $permissions)
            ->get();

        $existingPermissionsIds = $existingPermissions->pluck('id')->toArray();

        $index = 0;
        foreach ($permissions as $permission) {
            if (! in_array($permission, $existingPermissionsIds)) {
                throw new ValidationErrorsException([
                    "permissions.$index" => translate_error_message('permission', 'not_exists'),
                ]);
            }

            $index++;
        }

        return $existingPermissions;
    }
}
