<?php

namespace Modules\Auth\Enums;

enum GenderEnum
{
    const MALE = 1;

    const FEMALE = 0;

    public static function getValues(): array
    {
        return [self::MALE, self::FEMALE];
    }
}
