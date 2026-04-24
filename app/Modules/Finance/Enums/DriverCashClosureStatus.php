<?php

namespace App\Modules\Finance\Enums;

class DriverCashClosureStatus
{
    public const PENDING = 'pending';
    public const VERIFIED = 'verified';
    public const CANCELLED = 'cancelled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::PENDING,
            self::VERIFIED,
            self::CANCELLED,
        ];
    }
}
