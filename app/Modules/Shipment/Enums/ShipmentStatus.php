<?php

namespace App\Modules\Shipment\Enums;

class ShipmentStatus
{
    public const PENDING_PICKUP = 'pending_pickup';
    public const PICKED_UP = 'picked_up';
    public const IN_TRANSIT = 'in_transit';
    public const OUT_FOR_DELIVERY = 'out_for_delivery';
    public const DELIVERED = 'delivered';
    public const FAILED = 'failed';
    public const RETURNED = 'returned';
    public const CANCELLED = 'cancelled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::PENDING_PICKUP,
            self::PICKED_UP,
            self::IN_TRANSIT,
            self::OUT_FOR_DELIVERY,
            self::DELIVERED,
            self::FAILED,
            self::RETURNED,
            self::CANCELLED,
        ];
    }
}
