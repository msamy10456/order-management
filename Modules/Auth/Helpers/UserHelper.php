<?php

namespace Modules\Auth\Helpers;

use App\Models\User;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Exceptions\UserException;

class UserHelper
{
    /**
     * @throws UserException
     */
    public static function getUserById($id)
    {
        $user = User::query()->whereIn('type', [
            // UserTypeEnum::DOCTOR,
            // UserTypeEnum::PATIENT,
        ])
            ->find($id);

        if (! $user) {
            UserException::notExists();
        }

        return $user;
    }
}
