<?php

namespace Modules\Payment\Services\Contract;

use Modules\Payment\Services\Gateway\GatewayResponse;

interface PaymentGatewayInterface
{
    public function checkStatus($transactionId): GatewayResponse;
}
