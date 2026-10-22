<?php

namespace Modules\Auth\Helpers;

use Modules\Auth\Enums\UserTypeEnum;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class UserTypeHelper
{
    public static function nonMobileTypes(): array
    {
        return [
            UserTypeEnum::ADMIN,
            UserTypeEnum::ADMIN_EMPLOYEE,
        ];
    }

    public static function websiteTypes(): array
    {
        return [
            UserTypeEnum::USER,
        ];
    }

    public static function mobileTypes(): array
    {
        return [
            UserTypeEnum::USER,
        ];
    }

    public static function dashboardMobileTypes(): array
    {
        return [
            UserTypeEnum::ADMIN,
            UserTypeEnum::ADMIN_EMPLOYEE,
        ];
    }
}
