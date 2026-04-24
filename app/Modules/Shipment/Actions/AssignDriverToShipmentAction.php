<?php

namespace App\Modules\Shipment\Actions;

use App\Modules\Driver\Enums\DriverStatus;
use App\Modules\Driver\Models\Driver;
use App\Modules\Shipment\Enums\ShipmentStatus;
use App\Modules\Shipment\Models\Shipment;
use Illuminate\Http\Exceptions\HttpResponseException;

class AssignDriverToShipmentAction
{
    public function execute(Shipment $shipment, Driver $driver): Shipment
    {
        if ($driver->status !== DriverStatus::ACTIVE) {
            $this->fail('Selected driver is inactive.');
        }

        if ($shipment->status === ShipmentStatus::DELIVERED) {
            $this->fail('Delivered shipment cannot be reassigned.');
        }

        if ($shipment->status === ShipmentStatus::CANCELLED) {
            $this->fail('Cancelled shipment cannot be assigned.');
        }

        $shipment->update([
            'assigned_driver_id' => $driver->id,
        ]);

        return $shipment->fresh();
    }

    private function fail(string $message): never
    {
        throw new HttpResponseException(response()->json([
            'message' => $message,
        ], 422));
    }
}
