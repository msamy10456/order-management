<?php

namespace Modules\Auth\Actions\Register;

use App\Helpers\MediaHelper;
use Modules\Auth\Enums\AuthEnum;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Strategies\Verifiable;


class VendorUserRegister
{
    public function handle(array $data)
    {
        $data['type'] = UserTypeEnum::VENDOR;
        $vendor = (new BaseRegisterAction)->handle($data, app(Verifiable::class), byAdmin: true);
        MediaHelper::uploadMedia(
            $vendor,
            $data['avatar'] ?? null,
            AuthEnum::AVATAR_COLLECTION_NAME
        );
    }
}
