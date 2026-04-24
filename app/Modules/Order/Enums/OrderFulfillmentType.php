<?php

namespace App\Modules\Order\Enums;

class OrderFulfillmentType
{
    public const PICKUP_FROM_MERCHANT = 'pickup_from_merchant';
    public const FROM_WAREHOUSE = 'from_warehouse';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::PICKUP_FROM_MERCHANT,
            self::FROM_WAREHOUSE,
        ];
    }
}
