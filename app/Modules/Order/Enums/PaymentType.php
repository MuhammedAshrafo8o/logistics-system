<?php

namespace App\Modules\Order\Enums;

class PaymentType
{
    public const COD = 'cod';
    public const PREPAID = 'prepaid';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::COD,
            self::PREPAID,
        ];
    }
}
