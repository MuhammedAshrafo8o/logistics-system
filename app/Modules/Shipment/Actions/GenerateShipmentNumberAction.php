<?php

namespace App\Modules\Shipment\Actions;

use App\Modules\Shipment\Models\Shipment;

class GenerateShipmentNumberAction
{
    public function execute(): string
    {
        $datePart = now()->format('Ymd');
        $prefix = 'SHP-'.$datePart.'-';

        $latestShipmentNumber = Shipment::withTrashed()
            ->where('shipment_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('shipment_number')
            ->value('shipment_number');

        $nextSequence = $latestShipmentNumber === null
            ? 1
            : ((int) substr($latestShipmentNumber, -6)) + 1;

        return $prefix.str_pad((string) $nextSequence, 6, '0', STR_PAD_LEFT);
    }
}
