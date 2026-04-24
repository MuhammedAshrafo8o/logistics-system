<?php

namespace App\Modules\Warehouse\Enums;

class StockMovementType
{
    public const IN = 'in';
    public const OUT = 'out';
    public const RESERVED = 'reserved';
    public const RELEASED = 'released';
    public const DAMAGED = 'damaged';
    public const ADJUSTMENT = 'adjustment';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::IN,
            self::OUT,
            self::RESERVED,
            self::RELEASED,
            self::DAMAGED,
            self::ADJUSTMENT,
        ];
    }
}
