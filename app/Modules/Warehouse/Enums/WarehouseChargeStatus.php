<?php

namespace App\Modules\Warehouse\Enums;

class WarehouseChargeStatus
{
    public const PENDING = 'pending';
    public const INVOICED = 'invoiced';
    public const PAID = 'paid';
    public const CANCELLED = 'cancelled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::PENDING,
            self::INVOICED,
            self::PAID,
            self::CANCELLED,
        ];
    }
}
