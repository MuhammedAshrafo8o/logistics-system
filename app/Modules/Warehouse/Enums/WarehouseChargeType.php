<?php

namespace App\Modules\Warehouse\Enums;

class WarehouseChargeType
{
    public const STORAGE = 'storage';
    public const PACKAGING = 'packaging';
    public const FULFILLMENT = 'fulfillment';
    public const RETURN_HANDLING = 'return_handling';
    public const ADJUSTMENT = 'adjustment';
    public const OTHER = 'other';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::STORAGE,
            self::PACKAGING,
            self::FULFILLMENT,
            self::RETURN_HANDLING,
            self::ADJUSTMENT,
            self::OTHER,
        ];
    }
}
