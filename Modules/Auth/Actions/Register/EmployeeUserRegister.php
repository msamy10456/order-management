<?php

namespace Modules\Auth\Actions\Register;

use App\Helpers\MediaHelper;
use Modules\Auth\Enums\AuthEnum;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Strategies\Verifiable;


class EmployeeUserRegister
{
    public function handle(array $data)
    {
        $data['type'] = UserTypeEnum::ADMIN_EMPLOYEE;
        $employee = (new BaseRegisterAction)->handle($data, app(Verifiable::class), byAdmin: true);
        $employee->roles()->attach($data['role_id']);
        MediaHelper::uploadMedia(
            $employee,
            $data['avatar'] ?? null,
            AuthEnum::AVATAR_COLLECTION_NAME
        );
    }
}
