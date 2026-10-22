<?php

namespace Modules\Payment\Enum;

enum PaymentStatusEnum
{
    public const PENDING = 1;

    public const SUCCESSFUL = 2;

    public const FAILED = 3;

    public static function availableTypes(): array
    {
        return [
            self::PENDING,
            self::SUCCESSFUL,
            self::FAILED,
        ];
    }

    public static function parseStatus($status): ?int
    {
        if (is_numeric($status)) {
            return (int) $status;
        }

        return match (strtolower((string) $status)) {
            'pending' => self::PENDING,
            'successful' => self::SUCCESSFUL,
            'failed' => self::FAILED,
            default => null,
        };
    }
}
