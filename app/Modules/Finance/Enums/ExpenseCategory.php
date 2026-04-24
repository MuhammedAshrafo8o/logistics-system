<?php

namespace App\Modules\Finance\Enums;

class ExpenseCategory
{
    public const FUEL = 'fuel';
    public const SALARIES = 'salaries';
    public const RENT = 'rent';
    public const PACKAGING = 'packaging';
    public const MAINTENANCE = 'maintenance';
    public const OTHER = 'other';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::FUEL,
            self::SALARIES,
            self::RENT,
            self::PACKAGING,
            self::MAINTENANCE,
            self::OTHER,
        ];
    }
}
