<?php

namespace Modules\Payment\Enum;

enum PaymentMethodEnum
{
    public const PAYPAL = 'paypal';

    public const PAYMOB = 'paymob';

    public const CREDIT_CARD = 'credit_card';

    public static function availableTypes(): array
    {
        return [
            self::PAYPAL,
            self::PAYMOB,
            self::CREDIT_CARD,
        ];
    }
}
