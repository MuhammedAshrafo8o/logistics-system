<?php

namespace App\Modules\Driver\Enums;

class DriverStatus
{
    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
        ];
    }
}
