<?php

namespace Modules\Auth\Actions\Register;

use App\Models\User;
use Modules\Auth\Strategies\Verifiable;
use Illuminate\Support\Facades\Notification;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\FcmNotification\Enums\NotificationTypeEnum;
use Modules\FcmNotification\Notifications\FcmNotification;

class ClientUserRegister
{
    public function handle(array $data)
    {
        $data['type'] = UserTypeEnum::USER;
        $user = (new BaseRegisterAction)->handle($data, app(Verifiable::class));
    }
}
