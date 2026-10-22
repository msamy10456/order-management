<?php

namespace Modules\Auth\Actions\Register;

use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Strategies\Verifiable;

class ClientUserRegister
{
    public function handle(array $data)
    {
        $data['type'] = UserTypeEnum::USER;
        $user = (new BaseRegisterAction)->handle($data, app(Verifiable::class));
    }
}
