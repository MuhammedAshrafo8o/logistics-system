<?php

namespace App\Modules\Finance\Services;

use App\Models\Merchant;
use App\Modules\Finance\Enums\PayoutStatus;
use App\Modules\Finance\Enums\DriverCashClosureStatus;
use App\Modules\Finance\Models\MerchantPayout;
use App\Modules\Finance\Models\DriverCashClosure;
use App\Modules\Finance\Models\Expense;
use App\Modules\Finance\Models\MerchantInvoice;
use App\Modules\Order\Enums\PaymentType;
use App\Modules\Shipment\Enums\ShipmentStatus;
use App\Modules\Shipment\Models\Shipment;
use App\Modules\Driver\Models\Driver;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Warehouse\Enums\WarehouseChargeStatus;
use App\Modules\Warehouse\Models\WarehouseCharge;

class FinanceSummaryService
{
    public function getMerchantAvailableBalance(Merchant $merchant, ?MerchantPayout $excludePayout = null): float
    {
        $shipmentQuery = Shipment::query()
            ->where('merchant_id', $merchant->id)
            ->where('status', ShipmentStatus::DELIVERED);

        $deliveredShipments = $shipmentQuery->get(['cod_amount', 'shipping_fee']);

        $merchantPayable = $deliveredShipments->sum(function ($shipment): float {
            $codAmount = (float) $shipment->cod_amount;
            $shippingFee = (float) $shipment->shipping_fee;

            if ($codAmount <= 0) {
                return 0;
            }

            return max($codAmount - $shippingFee, 0);
        });

        $payoutQuery = MerchantPayout::query()
            ->where('merchant_id', $merchant->id)
            ->whereIn('status', [PayoutStatus::COMPLETED, PayoutStatus::PENDING]);

        if ($excludePayout !== null) {
            $payoutQuery->whereKeyNot($excludePayout->id);
        }

        $reservedPayouts = (float) $payoutQuery->sum('amount');

        return $merchantPayable - $reservedPayouts;
    }

    public function getDriverExpectedCash(Driver $driver): float
    {
        return (float) Shipment::query()
            ->where('assigned_driver_id', $driver->id)
            ->where('status', ShipmentStatus::DELIVERED)
            ->whereHas('order', fn (Builder $query) => $query->where('payment_type', PaymentType::COD))
            ->sum('cod_amount');
    }

    /**
     * @return array<string, mixed>
     */
    public function getMerchantInvoicePreview(MerchantInvoice $invoice): array
    {
        $merchant = $invoice->merchant()->firstOrFail();
        $shipments = Shipment::query()
            ->where('merchant_id', $merchant->id)
            ->where('status', ShipmentStatus::DELIVERED)
            ->when($invoice->period_start !== null, fn (Builder $query) => $query->whereDate('created_at', '>=', $invoice->period_start))
            ->when($invoice->period_end !== null, fn (Builder $query) => $query->whereDate('created_at', '<=', $invoice->period_end))
            ->with('order:id,payment_type')
            ->orderBy('created_at')
            ->get(['id', 'order_id', 'shipment_number', 'customer_name', 'customer_phone', 'cod_amount', 'shipping_fee', 'created_at']);

        return [
            'merchant' => [
                'id' => $merchant->id,
                'name' => $merchant->name,
                'phone' => $merchant->phone,
            ],
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'status' => $invoice->status,
            'period' => [
                'start' => $invoice->period_start?->format('Y-m-d'),
                'end' => $invoice->period_end?->format('Y-m-d'),
            ],
            'totals' => [
                'total_cod' => $invoice->total_cod,
                'total_shipping_fees' => $invoice->total_shipping_fees,
                'total_warehouse_charges' => $invoice->total_warehouse_charges,
                'total_payable' => $invoice->total_payable,
            ],
            'warehouse_charges' => WarehouseCharge::query()
                ->where('merchant_id', $merchant->id)
                ->where('status', '!=', WarehouseChargeStatus::CANCELLED)
                ->when($invoice->period_start !== null, fn (Builder $query) => $query->whereDate('charge_date', '>=', $invoice->period_start))
                ->when($invoice->period_end !== null, fn (Builder $query) => $query->whereDate('charge_date', '<=', $invoice->period_end))
                ->orderBy('charge_date')
                ->get()
                ->map(fn (WarehouseCharge $charge) => [
                    'id' => $charge->id,
                    'type' => $charge->type,
                    'description' => $charge->description,
                    'quantity' => $charge->quantity,
                    'unit_price' => $charge->unit_price,
                    'amount' => $charge->amount,
                    'status' => $charge->status,
                    'charge_date' => $charge->charge_date,
                    'notes' => $charge->notes,
                ])
                ->values(),
            'shipments' => $shipments->map(fn ($shipment) => [
                'shipment_id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
                'customer_name' => $shipment->customer_name,
                'customer_phone' => $shipment->customer_phone,
                'payment_type' => $shipment->order?->payment_type,
                'cod_amount' => $shipment->cod_amount,
                'shipping_fee' => $shipment->shipping_fee,
                'delivered_at' => $shipment->created_at,
            ])->values(),
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{total_cod:string,total_shipping_fees:string,total_warehouse_charges:string,total_payable:string}
     */
    public function calculateMerchantInvoiceTotals(Merchant $merchant, array $filters): array
    {
        $shipments = Shipment::query()
            ->where('merchant_id', $merchant->id)
            ->where('status', ShipmentStatus::DELIVERED)
            ->when(isset($filters['date_from']) && $filters['date_from'] !== null, fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']) && $filters['date_to'] !== null, fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->with('order:id,payment_type')
            ->get(['id', 'order_id', 'cod_amount', 'shipping_fee']);

        $totalCod = $shipments
            ->filter(fn ($shipment) => $shipment->order?->payment_type === PaymentType::COD)
            ->sum(fn ($shipment) => (float) $shipment->cod_amount);

        $totalShippingFees = $shipments->sum(fn ($shipment) => (float) $shipment->shipping_fee);
        $warehouseCharges = WarehouseCharge::query()
            ->where('merchant_id', $merchant->id)
            ->where('status', '!=', WarehouseChargeStatus::CANCELLED)
            ->when(isset($filters['date_from']) && $filters['date_from'] !== null, fn (Builder $query) => $query->whereDate('charge_date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']) && $filters['date_to'] !== null, fn (Builder $query) => $query->whereDate('charge_date', '<=', $filters['date_to']))
            ->sum('amount');

        $totalPayable = $shipments->sum(function ($shipment): float {
            $codAmount = (float) $shipment->cod_amount;
            $shippingFee = (float) $shipment->shipping_fee;

            if ($codAmount <= 0) {
                return 0;
            }

            return max($codAmount - $shippingFee, 0);
        });

        return [
            'total_cod' => number_format((float) $totalCod, 2, '.', ''),
            'total_shipping_fees' => number_format((float) $totalShippingFees, 2, '.', ''),
            'total_warehouse_charges' => number_format((float) $warehouseCharges, 2, '.', ''),
            'total_payable' => number_format((float) $totalPayable, 2, '.', ''),
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getMerchantSummary(Merchant $merchant, array $filters): array
    {
        $shipmentQuery = Shipment::query()
            ->where('merchant_id', $merchant->id)
            ->where('status', ShipmentStatus::DELIVERED)
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']));

        $deliveredShipments = (clone $shipmentQuery)
            ->with('order:id,payment_type')
            ->get(['id', 'order_id', 'cod_amount', 'shipping_fee']);

        $codCollected = $deliveredShipments
            ->filter(fn ($shipment) => $shipment->order?->payment_type === PaymentType::COD)
            ->sum(fn ($shipment) => (float) $shipment->cod_amount);

        $codDeliveredCount = $deliveredShipments
            ->filter(fn ($shipment) => $shipment->order?->payment_type === PaymentType::COD)
            ->count();

        $prepaidDeliveredCount = $deliveredShipments
            ->filter(fn ($shipment) => $shipment->order?->payment_type === PaymentType::PREPAID)
            ->count();

        $shippingFees = $deliveredShipments->sum(fn ($shipment) => (float) $shipment->shipping_fee);
        $warehouseCharges = (float) WarehouseCharge::query()
            ->where('merchant_id', $merchant->id)
            ->where('status', '!=', WarehouseChargeStatus::CANCELLED)
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('charge_date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('charge_date', '<=', $filters['date_to']))
            ->sum('amount');

        $merchantPayable = $deliveredShipments->sum(function ($shipment): float {
            $codAmount = (float) $shipment->cod_amount;
            $shippingFee = (float) $shipment->shipping_fee;

            if ($codAmount <= 0) {
                return 0;
            }

            return max($codAmount - $shippingFee, 0);
        });

        $payoutQuery = MerchantPayout::query()
            ->where('merchant_id', $merchant->id)
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']));

        $paidOut = (clone $payoutQuery)
            ->where('status', PayoutStatus::COMPLETED)
            ->sum('amount');

        $pendingPayouts = (clone $payoutQuery)
            ->where('status', PayoutStatus::PENDING)
            ->sum('amount');

        $remainingBalance = $merchantPayable - (float) $paidOut - (float) $pendingPayouts;

        return [
            'merchant_id' => $merchant->id,
            'merchant_name' => $merchant->name,
            'cod_collected' => number_format((float) $codCollected, 2, '.', ''),
            'shipping_fees' => number_format((float) $shippingFees, 2, '.', ''),
            'warehouse_charges' => number_format($warehouseCharges, 2, '.', ''),
            'merchant_payable' => number_format((float) $merchantPayable, 2, '.', ''),
            'paid_out' => number_format((float) $paidOut, 2, '.', ''),
            'pending_payouts' => number_format((float) $pendingPayouts, 2, '.', ''),
            'remaining_balance' => number_format($remainingBalance, 2, '.', ''),
            'company_profit_from_shipping' => number_format((float) $shippingFees, 2, '.', ''),
            'shipments' => [
                'delivered_count' => $deliveredShipments->count(),
                'cod_delivered_count' => $codDeliveredCount,
                'prepaid_delivered_count' => $prepaidDeliveredCount,
            ],
            'filters' => [
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getCompanyProfitSummary(array $filters): array
    {
        $shipmentQuery = Shipment::query()
            ->where('status', ShipmentStatus::DELIVERED)
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']));

        $deliveredShipments = (clone $shipmentQuery)
            ->with('order:id,payment_type')
            ->get(['id', 'order_id', 'cod_amount', 'shipping_fee']);

        $totalShippingFees = $deliveredShipments->sum(fn ($shipment) => (float) $shipment->shipping_fee);
        $totalWarehouseCharges = (float) WarehouseCharge::query()
            ->where('status', '!=', WarehouseChargeStatus::CANCELLED)
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('charge_date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('charge_date', '<=', $filters['date_to']))
            ->sum('amount');
        $totalCodCollected = $deliveredShipments
            ->filter(fn ($shipment) => $shipment->order?->payment_type === PaymentType::COD)
            ->sum(fn ($shipment) => (float) $shipment->cod_amount);

        $expenseQuery = Expense::query()
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('expense_date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('expense_date', '<=', $filters['date_to']));

        $totalExpenses = (float) $expenseQuery->sum('amount');

        return [
            'total_shipping_fees' => number_format((float) $totalShippingFees, 2, '.', ''),
            'total_warehouse_charges' => number_format($totalWarehouseCharges, 2, '.', ''),
            'total_expenses' => number_format($totalExpenses, 2, '.', ''),
            'net_company_profit' => number_format((float) $totalShippingFees + $totalWarehouseCharges - $totalExpenses, 2, '.', ''),
            'total_cod_collected' => number_format((float) $totalCodCollected, 2, '.', ''),
            'delivered_shipments_count' => $deliveredShipments->count(),
            'filters' => [
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getReconciliationSummary(array $filters): array
    {
        $shipmentQuery = Shipment::query()
            ->where('status', ShipmentStatus::DELIVERED)
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']));

        $deliveredShipments = (clone $shipmentQuery)
            ->with('order:id,payment_type')
            ->get(['id', 'order_id', 'cod_amount', 'shipping_fee']);

        $totalCodExpected = $deliveredShipments
            ->filter(fn ($shipment) => $shipment->order?->payment_type === PaymentType::COD)
            ->sum(fn ($shipment) => (float) $shipment->cod_amount);

        $totalMerchantPayable = $deliveredShipments->sum(function ($shipment): float {
            $codAmount = (float) $shipment->cod_amount;
            $shippingFee = (float) $shipment->shipping_fee;

            if ($codAmount <= 0) {
                return 0;
            }

            return max($codAmount - $shippingFee, 0);
        });

        $driverCashVerified = (float) DriverCashClosure::query()
            ->where('status', DriverCashClosureStatus::VERIFIED)
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->sum('received_amount');

        $merchantPaidOut = (float) MerchantPayout::query()
            ->where('status', PayoutStatus::COMPLETED)
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->sum('amount');

        return [
            'total_cod_expected' => number_format((float) $totalCodExpected, 2, '.', ''),
            'total_driver_cash_verified' => number_format($driverCashVerified, 2, '.', ''),
            'cod_difference' => number_format($driverCashVerified - (float) $totalCodExpected, 2, '.', ''),
            'total_merchant_payable' => number_format((float) $totalMerchantPayable, 2, '.', ''),
            'total_merchant_paid_out' => number_format($merchantPaidOut, 2, '.', ''),
            'merchant_balance_remaining' => number_format((float) $totalMerchantPayable - $merchantPaidOut, 2, '.', ''),
            'filters' => [
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
            ],
        ];
    }
}
