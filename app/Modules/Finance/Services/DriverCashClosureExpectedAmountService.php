<?php

namespace App\Modules\Finance\Services;

use App\Modules\Driver\Models\Driver;
use App\Modules\Order\Enums\PaymentType;
use App\Modules\Shipment\Enums\ShipmentStatus;
use App\Modules\Shipment\Models\Shipment;

class DriverCashClosureExpectedAmountService
{
    public function calculate(Driver $driver, string $date): string
    {
        $expectedAmount = (float) Shipment::query()
            ->where('assigned_driver_id', $driver->id)
            ->where('status', ShipmentStatus::DELIVERED)
            ->whereHas('order', fn ($query) => $query->where('payment_type', PaymentType::COD))
            ->whereDate('updated_at', $date)
            ->sum('cod_amount');

        return number_format($expectedAmount, 2, '.', '');
    }
}
