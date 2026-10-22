<?php

namespace Modules\Auth\Enums;

enum SocialAuthEnum
{
    const GOOGLE = 'google';

    const APPLE = 'apple';

    const FACEBOOK = 'facebook';

    public static function toArray(): array
    {
        return [
            self::GOOGLE,
            self::APPLE,
            self::FACEBOOK,
        ];
    }
}
