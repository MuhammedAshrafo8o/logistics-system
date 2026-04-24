<?php

namespace App\Modules\Finance\Enums;

class PayoutStatus
{
    public const PENDING = 'pending';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::PENDING,
            self::COMPLETED,
            self::CANCELLED,
        ];
    }
}
