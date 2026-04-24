<?php

namespace App\Modules\Warehouse\Controllers;

use App\Modules\Warehouse\Models\WarehouseCharge;
use App\Modules\Warehouse\Requests\ListWarehouseChargesRequest;
use App\Modules\Warehouse\Requests\StoreWarehouseChargeRequest;
use App\Modules\Warehouse\Requests\UpdateWarehouseChargeRequest;
use App\Modules\Warehouse\Resources\WarehouseChargeResource;
use App\Modules\Warehouse\Services\WarehouseChargeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseChargeController
{
    public function __construct(
        private readonly WarehouseChargeService $warehouseChargeService,
    ) {
    }

    public function index(ListWarehouseChargesRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $charges = WarehouseCharge::query()
            ->with($this->relations())
            ->when(isset($validated['merchant_id']), fn ($query) => $query->where('merchant_id', $validated['merchant_id']))
            ->when(isset($validated['warehouse_id']), fn ($query) => $query->where('warehouse_id', $validated['warehouse_id']))
            ->when(isset($validated['type']), fn ($query) => $query->where('type', $validated['type']))
            ->when(isset($validated['status']), fn ($query) => $query->where('status', $validated['status']))
            ->when(isset($validated['date_from']), fn ($query) => $query->whereDate('charge_date', '>=', $validated['date_from']))
            ->when(isset($validated['date_to']), fn ($query) => $query->whereDate('charge_date', '<=', $validated['date_to']))
            ->latest()
            ->get();

        return WarehouseChargeResource::collection($charges);
    }

    public function store(StoreWarehouseChargeRequest $request): JsonResponse
    {
        $charge = $this->warehouseChargeService->store($request->validated(), $request->user()?->id);

        return (new WarehouseChargeResource($charge))
            ->response()
            ->setStatusCode(201);
    }

    public function show(WarehouseCharge $warehouseCharge): WarehouseChargeResource
    {
        return new WarehouseChargeResource($warehouseCharge->load($this->relations()));
    }

    public function update(UpdateWarehouseChargeRequest $request, WarehouseCharge $warehouseCharge): WarehouseChargeResource
    {
        return new WarehouseChargeResource(
            $this->warehouseChargeService->update($warehouseCharge, $request->validated())
        );
    }

    public function destroy(WarehouseCharge $warehouseCharge): JsonResponse
    {
        $warehouseCharge->delete();

        return response()->json([
            'message' => 'Warehouse charge deleted successfully',
        ]);
    }

    /**
     * @return list<string>
     */
    private function relations(): array
    {
        return [
            'merchant',
            'warehouse',
            'warehouseProduct',
            'createdBy',
        ];
    }
}
