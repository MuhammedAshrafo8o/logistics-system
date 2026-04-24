<?php

namespace App\Modules\Shipment\Controllers;

use App\Modules\Driver\Models\Driver;
use App\Modules\Order\Models\Order;
use App\Modules\Shipment\Actions\AssignDriverToShipmentAction;
use App\Modules\Shipment\Actions\CreateShipmentFromOrderAction;
use App\Modules\Shipment\Models\Shipment;
use App\Modules\Shipment\Requests\AssignDriverRequest;
use App\Modules\Shipment\Requests\ListShipmentsRequest;
use App\Modules\Shipment\Requests\UpdateShipmentStatusRequest;
use App\Modules\Shipment\Resources\ShipmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ShipmentController
{
    public function __construct(
        private readonly AssignDriverToShipmentAction $assignDriverToShipmentAction,
        private readonly CreateShipmentFromOrderAction $createShipmentFromOrderAction,
    ) {
    }

    public function index(ListShipmentsRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $shipments = Shipment::query()
            ->with($this->relations())
            ->when(isset($validated['status']), fn ($query) => $query->where('status', $validated['status']))
            ->when(isset($validated['merchant_id']), fn ($query) => $query->where('merchant_id', $validated['merchant_id']))
            ->when(isset($validated['assigned_driver_id']), fn ($query) => $query->where('assigned_driver_id', $validated['assigned_driver_id']))
            ->when(isset($validated['delivery_governorate_id']), fn ($query) => $query->where('delivery_governorate_id', $validated['delivery_governorate_id']))
            ->when(isset($validated['delivery_area_id']), fn ($query) => $query->where('delivery_area_id', $validated['delivery_area_id']))
            ->when(isset($validated['date_from']), fn ($query) => $query->whereDate('created_at', '>=', $validated['date_from']))
            ->when(isset($validated['date_to']), fn ($query) => $query->whereDate('created_at', '<=', $validated['date_to']))
            ->when(isset($validated['search']), function ($query) use ($validated) {
                $search = $validated['search'];

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('shipment_number', 'like', '%'.$search.'%')
                        ->orWhere('customer_name', 'like', '%'.$search.'%')
                        ->orWhere('customer_phone', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->get();

        return ShipmentResource::collection($shipments);
    }

    public function show(Shipment $shipment): ShipmentResource
    {
        return new ShipmentResource($shipment->load($this->relations()));
    }

    public function createFromOrder(Request $request, Order $order): ShipmentResource
    {
        $shipment = $this->createShipmentFromOrderAction->execute($order, $request->user()?->id);

        return new ShipmentResource($shipment->load($this->relations()));
    }

    public function updateStatus(UpdateShipmentStatusRequest $request, Shipment $shipment): ShipmentResource
    {
        $shipment = DB::transaction(function () use ($request, $shipment) {
            $shipment->update([
                'status' => $request->string('status')->toString(),
            ]);

            $shipment->histories()->create([
                'status' => $request->string('status')->toString(),
                'notes' => $request->input('notes'),
                'changed_by' => $request->user()?->id,
            ]);

            return $shipment->fresh();
        });

        return new ShipmentResource($shipment->load($this->relations()));
    }

    public function assignDriver(AssignDriverRequest $request, Shipment $shipment): ShipmentResource
    {
        $driver = Driver::query()->findOrFail($request->integer('driver_id'));
        $shipment = $this->assignDriverToShipmentAction->execute($shipment, $driver);

        return new ShipmentResource($shipment->load($this->relations()));
    }

    /**
     * @return list<string>
     */
    private function relations(): array
    {
        return [
            'merchant',
            'deliveryGovernorate',
            'deliveryArea',
            'assignedDriver',
            'histories',
            'histories.changedBy',
        ];
    }
}
