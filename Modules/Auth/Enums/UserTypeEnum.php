<?php

namespace Modules\Auth\Enums;

use App\Models\User;

enum UserTypeEnum
{
    const ADMIN = 0;

    const USER = 1;

    const ADMIN_EMPLOYEE = 2;

    public static function availableTypes(): array
    {
        return [
            self::ADMIN,
            self::ADMIN_EMPLOYEE,
        ];
    }

    public static function getAvailable()
    {
        return [
            self::USER,
        ];
    }

    public static function getUserType(?User $user = null)
    {
        $user = ! is_null($user) ? $user : auth()->user();

        return $user?->type;
    }

    public static function getUser()
    {
        return auth()->user();
    }

    public static function alphaTypes(): array
    {
        return [
            self::ADMIN => 'admin',
            self::ADMIN_EMPLOYEE => 'admin_employee',
            self::USER => 'user',
        ];
    }
}
