<?php

namespace Modules\Role\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Role\Models\Builders\PermissionBuilder;
use Spatie\Permission\Models\Permission as ModelsPermission;

/**
 * @method static PermissionBuilder|Permission newModelQuery()
 * @method static PermissionBuilder|Permission newQuery()
 * @method static PermissionBuilder|Permission query()
 * @method static PermissionBuilder|Permission whereAccessible(bool $showInstitutionPermissions)
 *
 * @mixin \Eloquent
 */
class Permission extends ModelsPermission
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role_id',
    ];

    public function newEloquentBuilder($query)
    {
        return new PermissionBuilder($query);
    }
}
