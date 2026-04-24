<?php

namespace App\Modules\Finance\Enums;

class PaymentMethod
{
    public const CASH = 'cash';
    public const BANK_TRANSFER = 'bank_transfer';
    public const WALLET = 'wallet';
    public const INSTAPAY = 'instapay';
    public const OTHER = 'other';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::CASH,
            self::BANK_TRANSFER,
            self::WALLET,
            self::INSTAPAY,
            self::OTHER,
        ];
    }
}
