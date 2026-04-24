<?php

namespace App\Modules\Warehouse\Controllers;

use App\Modules\Warehouse\Models\Warehouse;
use App\Modules\Warehouse\Requests\StoreWarehouseRequest;
use App\Modules\Warehouse\Requests\UpdateWarehouseRequest;
use App\Modules\Warehouse\Resources\WarehouseResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseController
{
    public function index(): AnonymousResourceCollection
    {
        $warehouses = Warehouse::query()
            ->with(['governorate', 'area'])
            ->latest()
            ->get();

        return WarehouseResource::collection($warehouses);
    }

    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'active';

        $warehouse = Warehouse::create($data);

        return (new WarehouseResource($warehouse->load(['governorate', 'area'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Warehouse $warehouse): WarehouseResource
    {
        return new WarehouseResource($warehouse->load(['governorate', 'area']));
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): WarehouseResource
    {
        $warehouse->update($request->validated());

        return new WarehouseResource($warehouse->fresh()->load(['governorate', 'area']));
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $warehouse->delete();

        return response()->json([
            'message' => 'Warehouse deleted successfully',
        ]);
    }
}
