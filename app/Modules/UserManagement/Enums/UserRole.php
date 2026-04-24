<?php

namespace App\Modules\UserManagement\Enums;

class UserRole
{
    public const ADMIN = 'admin';
    public const STAFF = 'staff';
    public const WAREHOUSE = 'warehouse';
    public const FINANCE = 'finance';
    public const DRIVER = 'driver';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return [
            self::ADMIN,
            self::STAFF,
            self::WAREHOUSE,
            self::FINANCE,
            self::DRIVER,
        ];
    }
}
