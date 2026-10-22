<?php

namespace Modules\Role\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Modules\Role\Http\Requests\RoleRequest;
use Modules\Role\Services\RoleService;
use Modules\Role\Transformers\RoleResource;

class AdminRoleController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly RoleService $roleService) {}

    public function index()
    {
        $roles = $this->roleService->index();

        return $this->paginatedResponse($roles, RoleResource::class);
    }

    public function show($id)
    {
        $role = $this->roleService->show($id);

        return $this->resourceResponse(RoleResource::make($role));
    }

    public function store(RoleRequest $request)
    {
        $this->roleService->store($request->validated());

        return $this->createdResponse(message: translate_success_message('role', 'created'));
    }

    public function update(RoleRequest $request, $id)
    {
        $this->roleService->update($request->validated(), $id);

        return $this->okResponse(message: translate_success_message('role', 'updated'));
    }

    public function destroy($id)
    {
        $this->roleService->destroy($id);

        return $this->okResponse(message: translate_success_message('role', 'deleted'));
    }
}
