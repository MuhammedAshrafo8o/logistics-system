<?php

namespace App\Modules\Finance\Services;

use App\Models\Merchant;
use App\Modules\Driver\Models\Driver;
use App\Modules\Finance\Enums\DriverCashClosureStatus;
use App\Modules\Finance\Enums\PayoutStatus;
use App\Modules\Finance\Models\DriverCashClosure;
use App\Modules\Finance\Models\Expense;
use App\Modules\Finance\Models\MerchantPayout;
use App\Modules\Order\Enums\PaymentType;
use App\Modules\Order\Models\Order;
use App\Modules\Shipment\Enums\ShipmentStatus;
use App\Modules\Shipment\Models\Shipment;
use Illuminate\Database\Eloquent\Builder;

class FinanceReportService
{
    public function __construct(
        private readonly FinanceSummaryService $financeSummaryService,
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getOverviewReport(array $filters): array
    {
        $companyProfit = $this->financeSummaryService->getCompanyProfitSummary($filters);
        $reconciliation = $this->financeSummaryService->getReconciliationSummary($filters);

        $shipmentQuery = Shipment::query()
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->when(isset($filters['merchant_id']), fn (Builder $query) => $query->where('merchant_id', $filters['merchant_id']))
            ->when(isset($filters['driver_id']), fn (Builder $query) => $query->where('assigned_driver_id', $filters['driver_id']))
            ->when(isset($filters['status']), fn (Builder $query) => $query->where('status', $filters['status']));

        $orderQuery = Order::query()
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->when(isset($filters['merchant_id']), fn (Builder $query) => $query->where('merchant_id', $filters['merchant_id']));

        return [
            'total_orders' => $orderQuery->count(),
            'total_shipments' => (clone $shipmentQuery)->count(),
            'delivered_shipments' => (clone $shipmentQuery)->where('status', ShipmentStatus::DELIVERED)->count(),
            'cod_expected' => $reconciliation['total_cod_expected'],
            'cod_collected_verified' => $reconciliation['total_driver_cash_verified'],
            'shipping_fees' => $companyProfit['total_shipping_fees'],
            'warehouse_charges' => $companyProfit['total_warehouse_charges'],
            'merchant_payables' => $reconciliation['total_merchant_payable'],
            'merchant_paid_out' => $reconciliation['total_merchant_paid_out'],
            'merchant_remaining_balance' => $reconciliation['merchant_balance_remaining'],
            'expenses' => $companyProfit['total_expenses'],
            'company_net_profit' => $companyProfit['net_company_profit'],
            'filters' => [
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
                'merchant_id' => $filters['merchant_id'] ?? null,
                'driver_id' => $filters['driver_id'] ?? null,
                'status' => $filters['status'] ?? null,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getMerchantReport(Merchant $merchant, array $filters): array
    {
        $summary = $this->financeSummaryService->getMerchantSummary($merchant, $filters);
        $shipmentQuery = Shipment::query()
            ->where('merchant_id', $merchant->id)
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->when(isset($filters['status']), fn (Builder $query) => $query->where('status', $filters['status']));

        return [
            'merchant_id' => $merchant->id,
            'merchant_name' => $merchant->name,
            'shipments' => (clone $shipmentQuery)->count(),
            'delivered' => $summary['shipments']['delivered_count'],
            'cod' => $summary['cod_collected'],
            'shipping' => $summary['shipping_fees'],
            'warehouse_charges' => $summary['warehouse_charges'],
            'payable' => $summary['merchant_payable'],
            'paid_out' => $summary['paid_out'],
            'remaining' => $summary['remaining_balance'],
            'filters' => [
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
                'status' => $filters['status'] ?? null,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getDriversReport(array $filters): array
    {
        $drivers = Driver::query()
            ->when(isset($filters['driver_id']), fn (Builder $query) => $query->whereKey($filters['driver_id']))
            ->latest()
            ->get();

        $items = $drivers->map(function (Driver $driver) use ($filters): array {
            $shipmentQuery = Shipment::query()
                ->where('assigned_driver_id', $driver->id)
                ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
                ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
                ->when(isset($filters['status']), fn (Builder $query) => $query->where('status', $filters['status']));

            $deliveredQuery = (clone $shipmentQuery)
                ->where('status', ShipmentStatus::DELIVERED);

            $expectedCod = (clone $deliveredQuery)
                ->whereHas('order', fn (Builder $query) => $query->where('payment_type', PaymentType::COD))
                ->sum('cod_amount');

            $receivedCod = DriverCashClosure::query()
                ->where('driver_id', $driver->id)
                ->where('status', DriverCashClosureStatus::VERIFIED)
                ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
                ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
                ->sum('received_amount');

            return [
                'driver_id' => $driver->id,
                'driver_name' => $driver->name,
                'shipments' => (clone $shipmentQuery)->count(),
                'delivered' => (clone $deliveredQuery)->count(),
                'expected_cod' => number_format((float) $expectedCod, 2, '.', ''),
                'received_cod' => number_format((float) $receivedCod, 2, '.', ''),
                'differences' => number_format((float) $receivedCod - (float) $expectedCod, 2, '.', ''),
            ];
        })->values();

        return [
            'items' => $items,
            'filters' => [
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
                'driver_id' => $filters['driver_id'] ?? null,
                'status' => $filters['status'] ?? null,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getExpensesReport(array $filters): array
    {
        $query = Expense::query()
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('expense_date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('expense_date', '<=', $filters['date_to']))
            ->when(isset($filters['status']), fn (Builder $query) => $query->where('category', $filters['status']));

        $expenses = $query->get(['category', 'amount']);

        return [
            'total' => number_format((float) $expenses->sum(fn ($expense) => (float) $expense->amount), 2, '.', ''),
            'by_category' => $expenses
                ->groupBy('category')
                ->map(fn ($group, $category) => [
                    'category' => $category,
                    'amount' => number_format((float) $group->sum(fn ($expense) => (float) $expense->amount), 2, '.', ''),
                ])
                ->values(),
            'filters' => [
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
                'status' => $filters['status'] ?? null,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getPayoutsReport(array $filters): array
    {
        $query = MerchantPayout::query()
            ->when(isset($filters['date_from']), fn (Builder $query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn (Builder $query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->when(isset($filters['merchant_id']), fn (Builder $query) => $query->where('merchant_id', $filters['merchant_id']))
            ->when(isset($filters['status']), fn (Builder $query) => $query->where('status', $filters['status']));

        return [
            'total_payouts' => number_format((float) (clone $query)->sum('amount'), 2, '.', ''),
            'pending' => number_format((float) (clone $query)->where('status', PayoutStatus::PENDING)->sum('amount'), 2, '.', ''),
            'completed' => number_format((float) (clone $query)->where('status', PayoutStatus::COMPLETED)->sum('amount'), 2, '.', ''),
            'filters' => [
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
                'merchant_id' => $filters['merchant_id'] ?? null,
                'status' => $filters['status'] ?? null,
            ],
        ];
    }
}
