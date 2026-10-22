<?php

namespace Modules\Role\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Order\Helpers\OrderPermissionDefinition;
use Modules\Payment\Helpers\PaymentPermissionDefinition;
use Modules\Role\Helpers\PermissionCacheHelper;
use Modules\Role\Helpers\PermissionHelper;
use Modules\Role\Helpers\RolePermissionDefinition;
use Spatie\Permission\Models\Role;

class RoleDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        $this->createRoles();
    }

    public function createPermissions()
    {
        $allPermissions = [
            ...RolePermissionDefinition::permissions(),
            ...OrderPermissionDefinition::permissions(),
            ...PaymentPermissionDefinition::permissions(),
        ];

        $latestPermissions = [];

        foreach ($allPermissions as $parentPermission => $operations) {
            $permission = [];
            foreach ($operations as $operation) {
                $permissionName = PermissionHelper::generatePermissionName($operation, $parentPermission);
                $permission['name'] = $permissionName;

                PermissionHelper::permissionModel()::firstOrCreate([
                    'name' => $permission['name'],
                    'guard_name' => 'web'
            ], $permission);
                $latestPermissions[] = $permission;
            }
        }

        PermissionCacheHelper::setCachedPermissions();

        $permissions = collect($latestPermissions)->pluck('name')->toArray();

        return PermissionHelper::permissionModel()::whereIn('name', $permissions)->get();
    }

    private function createRoles()
    {
        $allPermissions = $this->createPermissions();

        $role = Role::query()->firstOrCreate(['name' => 'admin' ,'guard_name' => 'web'], ['name' => 'admin','guard_name' => 'web']);

        $role->permissions()->sync($allPermissions->pluck('id')->toArray());
    }
}
