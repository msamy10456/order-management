<?php

return [
    'default' => env('DEFAULT_PAYMENT_GATEWAY', 'credit_card'),

    'gateways' => [
        'credit_card' => [
            'class' => \Modules\Payment\Gateways\CreditCardGateway::class,
            'api_key' => env('CREDIT_CARD_API_KEY', 'sk_test_mock_credit_card_key'),
            'secret' => env('CREDIT_CARD_SECRET', 'mock_secret'),
        ],
        'paypal' => [
            'class' => \Modules\Payment\Gateways\PaypalGateway::class,
            'client_id' => env('PAYPAL_CLIENT_ID', 'mock_paypal_client_id'),
            'secret' => env('PAYPAL_SECRET', 'mock_paypal_secret'),
            'sandbox' => env('PAYPAL_SANDBOX', true),
        ],
        'paymob' => [
            'class' => \Modules\Payment\Gateways\PaymobGateway::class,
            'api_key' => env('PAYMOB_API_KEY', 'mock_paymob_api_key'),
            'integration_id' => env('PAYMOB_INTEGRATION_ID', '123456'),
        ],
    ],
];
