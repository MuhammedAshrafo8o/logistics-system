<?php

namespace App\Modules\Order\Enums;

class OrderSource
{
    public const MANUAL = 'manual';
    public const INTEGRATION = 'integration';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::MANUAL,
            self::INTEGRATION,
        ];
    }
}
