<?php

namespace App\Modules\Warehouse\Controllers;

use App\Modules\Warehouse\Models\WarehouseReturn;
use App\Modules\Warehouse\Requests\ListWarehouseReturnsRequest;
use App\Modules\Warehouse\Requests\StoreWarehouseReturnRequest;
use App\Modules\Warehouse\Resources\WarehouseReturnResource;
use App\Modules\Warehouse\Services\WarehouseReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseReturnController
{
    public function __construct(
        private readonly WarehouseReturnService $warehouseReturnService,
    ) {
    }

    public function index(ListWarehouseReturnsRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $returns = WarehouseReturn::query()
            ->with($this->relations())
            ->when(isset($validated['shipment_id']), fn ($query) => $query->where('shipment_id', $validated['shipment_id']))
            ->when(isset($validated['order_id']), fn ($query) => $query->where('order_id', $validated['order_id']))
            ->when(isset($validated['warehouse_id']), fn ($query) => $query->where('warehouse_id', $validated['warehouse_id']))
            ->when(isset($validated['warehouse_product_id']), fn ($query) => $query->where('warehouse_product_id', $validated['warehouse_product_id']))
            ->when(isset($validated['condition']), fn ($query) => $query->where('condition', $validated['condition']))
            ->when(isset($validated['merchant_id']), fn ($query) => $query->whereHas('warehouseProduct', fn ($inner) => $inner->where('merchant_id', $validated['merchant_id'])))
            ->when(isset($validated['date_from']), fn ($query) => $query->whereDate('created_at', '>=', $validated['date_from']))
            ->when(isset($validated['date_to']), fn ($query) => $query->whereDate('created_at', '<=', $validated['date_to']))
            ->latest()
            ->get();

        return WarehouseReturnResource::collection($returns);
    }

    public function store(StoreWarehouseReturnRequest $request): JsonResponse
    {
        $warehouseReturn = $this->warehouseReturnService->store(
            $request->validated(),
            $request->user()?->id,
        );

        return (new WarehouseReturnResource($warehouseReturn))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WarehouseReturn $warehouseReturn): WarehouseReturnResource
    {
        return new WarehouseReturnResource($warehouseReturn->load($this->relations()));
    }

    /**
     * @return list<string>
     */
    private function relations(): array
    {
        return [
            'warehouse',
            'warehouseProduct.merchant',
            'createdBy',
        ];
    }
}
