<?php

namespace App\Modules\Finance\Enums;

class MerchantInvoiceStatus
{
    public const DRAFT = 'draft';
    public const ISSUED = 'issued';
    public const PAID = 'paid';
    public const CANCELLED = 'cancelled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::DRAFT,
            self::ISSUED,
            self::PAID,
            self::CANCELLED,
        ];
    }
}
