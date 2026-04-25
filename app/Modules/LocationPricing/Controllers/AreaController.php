<?php

namespace App\Modules\LocationPricing\Controllers;

use App\Modules\LocationPricing\Models\Area;
use App\Modules\LocationPricing\Requests\ListAreasRequest;
use App\Modules\LocationPricing\Requests\StoreAreaRequest;
use App\Modules\LocationPricing\Requests\UpdateAreaRequest;
use App\Modules\LocationPricing\Resources\AreaResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AreaController
{
    public function index(ListAreasRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $areas = Area::query()
            ->with('governorate')
            ->when(isset($validated['governorate_id']), fn ($query) => $query->where('governorate_id', $validated['governorate_id']))
            ->latest()
            ->get();

        return AreaResource::collection($areas);
    }

    public function store(StoreAreaRequest $request): JsonResponse
    {
        $area = Area::create([
            'governorate_id' => $request->integer('governorate_id'),
            'name' => $request->string('name')->toString(),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return (new AreaResource($area->load('governorate')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Area $area): AreaResource
    {
        return new AreaResource($area->load('governorate'));
    }

    public function update(UpdateAreaRequest $request, Area $area): AreaResource
    {
        $validated = $request->validated();

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $area->update($validated);

        return new AreaResource($area->fresh()->load('governorate'));
    }

    public function destroy(Area $area): JsonResponse
    {
        $area->delete();

        return response()->json([
            'message' => 'Area deleted successfully',
        ]);
    }
}
