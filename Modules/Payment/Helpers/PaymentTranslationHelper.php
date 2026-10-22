<?php

namespace Modules\Payment\Helpers;

class PaymentTranslationHelper
{
    public static function en()
    {
        return [
            'payment' => 'Payment',
            'payment_declined' => 'Payment was declined',
        ];
    }

    public static function ar()
    {
        return [
            'payment' => 'الدفع',
            'payment_declined' => 'تم رفض الدفع',
        ];
    }
}
