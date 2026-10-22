<?php

namespace Modules\Role\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface HasPermissionContract
{
    public function permissions(): BelongsToMany;

    public function hasPermission(string $permissionName): bool;
}
