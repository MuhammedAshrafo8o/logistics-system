<?php

namespace App\Modules\LocationPricing\Controllers;

use App\Modules\LocationPricing\Models\Governorate;
use App\Modules\LocationPricing\Requests\StoreGovernorateRequest;
use App\Modules\LocationPricing\Requests\UpdateGovernorateRequest;
use App\Modules\LocationPricing\Resources\GovernorateResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GovernorateController
{
    public function index(): AnonymousResourceCollection
    {
        $governorates = Governorate::query()->latest()->get();

        return GovernorateResource::collection($governorates);
    }

    public function store(StoreGovernorateRequest $request): JsonResponse
    {
        $governorate = Governorate::create([
            'name' => $request->string('name')->toString(),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return (new GovernorateResource($governorate))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Governorate $governorate): GovernorateResource
    {
        return new GovernorateResource($governorate);
    }

    public function update(UpdateGovernorateRequest $request, Governorate $governorate): GovernorateResource
    {
        $validated = $request->validated();

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $governorate->update($validated);

        return new GovernorateResource($governorate->fresh());
    }

    public function destroy(Governorate $governorate): JsonResponse
    {
        $governorate->delete();

        return response()->json([
            'message' => 'Governorate deleted successfully',
        ]);
    }
}
