<?php

namespace App\Modules\Warehouse\Controllers;

use App\Models\Merchant;
use App\Modules\Warehouse\Models\WarehouseCharge;
use App\Modules\Warehouse\Models\InventoryStock;
use App\Modules\Warehouse\Models\StockMovement;
use App\Modules\Warehouse\Resources\WarehouseChargeResource;
use App\Modules\Warehouse\Resources\InventoryStockResource;
use App\Modules\Warehouse\Resources\StockMovementResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MerchantWarehouseController
{
    public function inventory(Merchant $merchant): AnonymousResourceCollection
    {
        $stocks = InventoryStock::query()
            ->with(['warehouse', 'warehouseProduct.merchant'])
            ->whereHas('warehouseProduct', fn ($query) => $query->where('merchant_id', $merchant->id))
            ->latest()
            ->get();

        return InventoryStockResource::collection($stocks);
    }

    public function movements(Request $request, Merchant $merchant): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'warehouse_id' => ['nullable', 'integer'],
            'warehouse_product_id' => ['nullable', 'integer'],
            'type' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $movements = StockMovement::query()
            ->with(['warehouse', 'warehouseProduct.merchant', 'createdBy'])
            ->whereHas('warehouseProduct', fn ($query) => $query->where('merchant_id', $merchant->id))
            ->when(isset($validated['warehouse_id']), fn ($query) => $query->where('warehouse_id', $validated['warehouse_id']))
            ->when(isset($validated['warehouse_product_id']), fn ($query) => $query->where('warehouse_product_id', $validated['warehouse_product_id']))
            ->when(isset($validated['type']), fn ($query) => $query->where('type', $validated['type']))
            ->when(isset($validated['date_from']), fn ($query) => $query->whereDate('created_at', '>=', $validated['date_from']))
            ->when(isset($validated['date_to']), fn ($query) => $query->whereDate('created_at', '<=', $validated['date_to']))
            ->latest()
            ->get();

        return StockMovementResource::collection($movements);
    }

    public function charges(Request $request, Merchant $merchant): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'warehouse_id' => ['nullable', 'integer'],
            'type' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $charges = WarehouseCharge::query()
            ->with(['merchant', 'warehouse', 'warehouseProduct', 'createdBy'])
            ->where('merchant_id', $merchant->id)
            ->when(isset($validated['warehouse_id']), fn ($query) => $query->where('warehouse_id', $validated['warehouse_id']))
            ->when(isset($validated['type']), fn ($query) => $query->where('type', $validated['type']))
            ->when(isset($validated['status']), fn ($query) => $query->where('status', $validated['status']))
            ->when(isset($validated['date_from']), fn ($query) => $query->whereDate('charge_date', '>=', $validated['date_from']))
            ->when(isset($validated['date_to']), fn ($query) => $query->whereDate('charge_date', '<=', $validated['date_to']))
            ->latest()
            ->get();

        return WarehouseChargeResource::collection($charges);
    }
}
