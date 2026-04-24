<?php

namespace App\Modules\Warehouse\Enums;

class OrderStockReservationStatus
{
    public const RESERVED = 'reserved';
    public const FULFILLED = 'fulfilled';
    public const RELEASED = 'released';
    public const CANCELLED = 'cancelled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::RESERVED,
            self::FULFILLED,
            self::RELEASED,
            self::CANCELLED,
        ];
    }
}
