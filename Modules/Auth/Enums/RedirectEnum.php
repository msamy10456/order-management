<?php

namespace Modules\Auth\Enums;

use App\Models\User;

enum RedirectEnum
{
    const NOT_VERIFIED = 'not_verified';
    const ACCOUNT_SUSPENDED = 'account_suspended';
}
