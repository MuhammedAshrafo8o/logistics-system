<?php

namespace App\Modules\Warehouse\Services;

use App\Modules\Warehouse\Enums\StockMovementType;
use App\Modules\Warehouse\Enums\WarehouseReturnCondition;
use App\Modules\Warehouse\Models\InventoryStock;
use App\Modules\Warehouse\Models\StockMovement;
use App\Modules\Warehouse\Models\WarehouseReturn;
use Illuminate\Support\Facades\DB;

class WarehouseReturnService
{
    public function store(array $data, ?int $userId): WarehouseReturn
    {
        return DB::transaction(function () use ($data, $userId) {
            $stock = InventoryStock::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    [
                        'warehouse_id' => $data['warehouse_id'],
                        'warehouse_product_id' => $data['warehouse_product_id'],
                    ],
                    [
                        'quantity_available' => 0,
                        'quantity_reserved' => 0,
                        'quantity_damaged' => 0,
                    ]
                );

            $beforeAvailable = $stock->quantity_available;
            $beforeReserved = $stock->quantity_reserved;
            $beforeDamaged = $stock->quantity_damaged;

            $afterAvailable = $beforeAvailable;
            $afterReserved = $beforeReserved;
            $afterDamaged = $beforeDamaged;
            $movementType = StockMovementType::ADJUSTMENT;

            if ($data['condition'] === WarehouseReturnCondition::SELLABLE) {
                $afterAvailable += (int) $data['quantity'];
                $movementType = StockMovementType::IN;
            } elseif ($data['condition'] === WarehouseReturnCondition::DAMAGED) {
                $afterDamaged += (int) $data['quantity'];
                $movementType = StockMovementType::DAMAGED;
            }

            $stock->update([
                'quantity_available' => $afterAvailable,
                'quantity_reserved' => $afterReserved,
                'quantity_damaged' => $afterDamaged,
            ]);

            $warehouseReturn = WarehouseReturn::create([
                'shipment_id' => $data['shipment_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'warehouse_product_id' => $data['warehouse_product_id'],
                'quantity' => $data['quantity'],
                'condition' => $data['condition'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            StockMovement::create([
                'warehouse_id' => $data['warehouse_id'],
                'warehouse_product_id' => $data['warehouse_product_id'],
                'type' => $movementType,
                'quantity' => $data['quantity'],
                'before_available' => $beforeAvailable,
                'after_available' => $afterAvailable,
                'before_reserved' => $beforeReserved,
                'after_reserved' => $afterReserved,
                'before_damaged' => $beforeDamaged,
                'after_damaged' => $afterDamaged,
                'reference_type' => 'warehouse_return',
                'reference_id' => $warehouseReturn->id,
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            return $warehouseReturn->load([
                'warehouse',
                'warehouseProduct.merchant',
                'createdBy',
            ]);
        });
    }
}
