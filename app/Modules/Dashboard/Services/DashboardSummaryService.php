<?php

namespace App\Modules\Dashboard\Services;

use App\Modules\Shipment\Enums\ShipmentStatus;
use App\Modules\Shipment\Models\Shipment;
use Illuminate\Database\Eloquent\Builder;

class DashboardSummaryService
{
    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getSummary(array $filters): array
    {
        $baseQuery = Shipment::query()
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->when(isset($filters['merchant_id']), fn (Builder $query) => $query->where('merchant_id', $filters['merchant_id']))
            ->when(isset($filters['assigned_driver_id']), fn (Builder $query) => $query->where('assigned_driver_id', $filters['assigned_driver_id']));

        $deliveredShipments = (clone $baseQuery)
            ->where('status', ShipmentStatus::DELIVERED)
            ->get(['cod_amount', 'shipping_fee']);

        $companyProfit = $deliveredShipments->sum(fn ($shipment) => (float) $shipment->shipping_fee);
        $merchantsPayables = $deliveredShipments->sum(function ($shipment): float {
            $codAmount = (float) $shipment->cod_amount;
            $shippingFee = (float) $shipment->shipping_fee;

            if ($codAmount <= 0) {
                return 0;
            }

            return max($codAmount - $shippingFee, 0);
        });
        $codCollected = $deliveredShipments->sum(fn ($shipment) => (float) $shipment->cod_amount);

        return [
            'company_profit' => number_format($companyProfit, 2, '.', ''),
            'merchants_payables' => number_format($merchantsPayables, 2, '.', ''),
            'cod_collected' => number_format($codCollected, 2, '.', ''),
            'shipments' => [
                'total' => (clone $baseQuery)->count(),
                'pending_pickup' => (clone $baseQuery)->where('status', ShipmentStatus::PENDING_PICKUP)->count(),
                'in_transit' => (clone $baseQuery)->whereIn('status', [
                    ShipmentStatus::PENDING_PICKUP,
                    ShipmentStatus::PICKED_UP,
                    ShipmentStatus::IN_TRANSIT,
                    ShipmentStatus::OUT_FOR_DELIVERY,
                ])->count(),
                'delivered' => (clone $baseQuery)->where('status', ShipmentStatus::DELIVERED)->count(),
                'assigned' => (clone $baseQuery)->whereNotNull('assigned_driver_id')->count(),
                'unassigned' => (clone $baseQuery)->whereNull('assigned_driver_id')->count(),
            ],
            'filters' => [
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
                'merchant_id' => $filters['merchant_id'] ?? null,
                'assigned_driver_id' => $filters['assigned_driver_id'] ?? null,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getMerchantSummary(int $merchantId, array $filters): array
    {
        $baseQuery = Shipment::query()
            ->where('merchant_id', $merchantId)
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->when(isset($filters['assigned_driver_id']), fn (Builder $query) => $query->where('assigned_driver_id', $filters['assigned_driver_id']));

        $deliveredShipments = (clone $baseQuery)
            ->where('status', ShipmentStatus::DELIVERED)
            ->get(['cod_amount', 'shipping_fee']);

        $companyProfit = $deliveredShipments->sum(fn ($shipment) => (float) $shipment->shipping_fee);
        $merchantsPayables = $deliveredShipments->sum(function ($shipment): float {
            $codAmount = (float) $shipment->cod_amount;
            $shippingFee = (float) $shipment->shipping_fee;

            if ($codAmount <= 0) {
                return 0;
            }

            return max($codAmount - $shippingFee, 0);
        });

        $codCollected = (clone $baseQuery)
            ->where('status', ShipmentStatus::DELIVERED)
            ->whereHas('order', fn (Builder $query) => $query->where('payment_type', 'cod'))
            ->sum('cod_amount');

        return [
            'company_profit' => number_format($companyProfit, 2, '.', ''),
            'merchants_payables' => number_format($merchantsPayables, 2, '.', ''),
            'cod_collected' => number_format((float) $codCollected, 2, '.', ''),
            'shipments' => [
                'total' => (clone $baseQuery)->count(),
                'pending_pickup' => (clone $baseQuery)->where('status', ShipmentStatus::PENDING_PICKUP)->count(),
                'in_transit' => (clone $baseQuery)->whereIn('status', [
                    ShipmentStatus::PENDING_PICKUP,
                    ShipmentStatus::PICKED_UP,
                    ShipmentStatus::IN_TRANSIT,
                    ShipmentStatus::OUT_FOR_DELIVERY,
                ])->count(),
                'delivered' => (clone $baseQuery)->where('status', ShipmentStatus::DELIVERED)->count(),
                'assigned' => (clone $baseQuery)->whereNotNull('assigned_driver_id')->count(),
                'unassigned' => (clone $baseQuery)->whereNull('assigned_driver_id')->count(),
            ],
            'filters' => [
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
                'merchant_id' => $merchantId,
                'assigned_driver_id' => $filters['assigned_driver_id'] ?? null,
            ],
        ];
    }
}
