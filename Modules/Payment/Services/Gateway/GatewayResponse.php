<?php

namespace Modules\Payment\Services\Gateway;

final class GatewayResponse
{
    public function __construct(
        public readonly bool $successful,
        public readonly ?string $transactionId = null,
        public readonly string $message = '',
        public readonly array $raw = [],
    ) {
    }
    public static function success(string $transactionId, string $message = 'Payment successful.', array $raw = []): self
    {
        return new self(true, $transactionId, $message, $raw);
    }
    public static function failure(string $message, array $raw = []): self
    {
        return new self(false, null, $message, $raw);
    }
}
