<?php

namespace Modules\Order\Enum;

enum OrderStatusEnum
{
    public const PENDING = 1;
    public const CONFIRMED = 2;
    public const CANCELLED = 3;

    public const ACTIVE = 1;
    public const DISACTIVE = 2;

    public static function availableTypes(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::CANCELLED,
        ];
    }

    public static function parseStatus($status): ?int
    {
        if (is_numeric($status)) {
            return (int) $status;
        }

        return match (strtolower((string) $status)) {
            'pending' => self::PENDING,
            'confirmed' => self::CONFIRMED,
            'cancelled' => self::CANCELLED,
            default => null,
        };
    }
}
