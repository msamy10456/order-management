<?php

namespace Modules\Role\Services;

use App\Exceptions\ValidationErrorsException;
use Illuminate\Support\Facades\DB;
use Modules\Role\Exceptions\RoleException;
use Modules\Role\Models\Role;

readonly class RoleService
{
    public function __construct(private Role $roleModel, private PermissionService $permissionService) {}

    public function index()
    {
        return $this->roleModel::query()
            ->where('id', '<>', 1)
            ->latest()
            ->searchable(['name'])
            ->paginatedCollection();
    }

    public function show($id)
    {
        return $this->roleModel::query()
            ->where('id', '<>', 1)
            ->with('permissions:id,name')
            ->findOrFail($id);
    }

    /**
     * @throws RoleException
     * @throws \Throwable
     */
    public function store(array $data)
    {
        $this->assertNameExists($data['name']);

        $this->permissionService->exist($data['permissions']);

        DB::transaction(function () use ($data) {
            $role = $this->roleModel::create($data);

            $role->permissions()->attach($data['permissions']);
        });
    }

    /**
     * @throws RoleException
     * @throws \Throwable
     * @throws ValidationErrorsException
     */
    public function update(array $data, $id)
    {
        if (isset($data['name'])) {
            $this->assertNameExists($data['name'], $id);
        }

        if (isset($data['permissions'])) {
            $this->permissionService->exist($data['permissions']);
        }

        DB::transaction(function () use ($id, $data) {
            $role = $this->roleModel::query()
                ->where('id', '<>', 1)
                ->findOrFail($id);

            $role->update($data);

            if (isset($data['permissions'])) {
                $role->permissions()->sync($data['permissions']);
            }
        });

    }

    public function destroy($id)
    {
        $this->roleModel::query()
            ->where('id', '<>', 1)
            ->findOrFail($id)
            ->delete();
    }

    /**
     * @throws RoleException
     * @throws ValidationErrorsException
     */
    private function assertNameExists($name, $id = null): void
    {
        $exists = $this->roleModel::query()
            ->where('name', $name)
            ->when(! is_null($id), fn ($query) => $query->where('id', '<>', $id))
            ->exists();

        if ($exists) {
            throw new ValidationErrorsException([
                'name' => translate_error_message('role', 'exists'),
            ]);
        }
    }

    /**
     * @throws ValidationErrorsException
     */
    public static function exists($roleId, string $errorKey = 'role_id')
    {
        $role = Role::query()
            ->where('id', '<>', 1)
            ->where('status', true)
            ->find($roleId);

        if (! $role) {
            throw new ValidationErrorsException([
                $errorKey => translate_error_message('role', 'not_exists'),
            ]);
        }

        return $role;
    }
}
