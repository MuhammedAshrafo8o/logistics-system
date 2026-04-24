<?php

namespace App\Modules\Warehouse\Controllers;

use App\Modules\Warehouse\Models\StockMovement;
use App\Modules\Warehouse\Requests\ListStockMovementsRequest;
use App\Modules\Warehouse\Resources\StockMovementResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockMovementController
{
    public function index(ListStockMovementsRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $movements = StockMovement::query()
            ->with(['warehouse', 'warehouseProduct.merchant', 'createdBy'])
            ->when(isset($validated['warehouse_id']), fn ($query) => $query->where('warehouse_id', $validated['warehouse_id']))
            ->when(isset($validated['warehouse_product_id']), fn ($query) => $query->where('warehouse_product_id', $validated['warehouse_product_id']))
            ->when(isset($validated['merchant_id']), fn ($query) => $query->whereHas('warehouseProduct', fn ($inner) => $inner->where('merchant_id', $validated['merchant_id'])))
            ->when(isset($validated['type']), fn ($query) => $query->where('type', $validated['type']))
            ->when(isset($validated['date_from']), fn ($query) => $query->whereDate('created_at', '>=', $validated['date_from']))
            ->when(isset($validated['date_to']), fn ($query) => $query->whereDate('created_at', '<=', $validated['date_to']))
            ->latest()
            ->get();

        return StockMovementResource::collection($movements);
    }

    public function show(StockMovement $stockMovement): StockMovementResource
    {
        return new StockMovementResource($stockMovement->load(['warehouse', 'warehouseProduct.merchant', 'createdBy']));
    }
}
