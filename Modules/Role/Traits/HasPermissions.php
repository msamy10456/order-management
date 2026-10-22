<?php

namespace Modules\Role\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Role\Models\Permission;
use Modules\Role\Models\Role;

trait HasPermissions
{
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission(string $permissionName): bool
    {
        return auth()->user()->roles()->first()->permissions()->where('name', $permissionName)->exists();
    }

    public function assignRole($role)
    {
        $role = Role::where('name', $role)->first();

        if ($role) {
            $this->roles()->sync($role->id);
        }
    }
}
