<?php

namespace Modules\Auth\Enums;

enum VerifyTokenTypeEnum
{
    const VERIFICATION = 0;

    const PASSWORD_RESET = 1;

    const UPDATE_PHONE_NUMBER = 2;

    public static function availableTypes(): array
    {
        return [
            self::VERIFICATION,
            self::PASSWORD_RESET,
            self::UPDATE_PHONE_NUMBER,
        ];
    }
}
