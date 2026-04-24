<?php

namespace App\Modules\Finance\Actions;

use App\Models\Merchant;
use App\Modules\Finance\Enums\MerchantInvoiceStatus;
use App\Modules\Finance\Models\MerchantInvoice;
use App\Modules\Finance\Services\FinanceSummaryService;
use Illuminate\Support\Facades\DB;

class CreateMerchantInvoiceAction
{
    public function __construct(
        private readonly FinanceSummaryService $financeSummaryService,
        private readonly GenerateMerchantInvoiceNumberAction $generateMerchantInvoiceNumberAction,
    ) {
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function execute(Merchant $merchant, array $validated, ?int $createdBy): MerchantInvoice
    {
        return DB::transaction(function () use ($merchant, $validated, $createdBy) {
            $totals = $this->financeSummaryService->calculateMerchantInvoiceTotals($merchant, [
                'date_from' => $validated['period_start'] ?? null,
                'date_to' => $validated['period_end'] ?? null,
            ]);

            $status = $validated['status'] ?? MerchantInvoiceStatus::DRAFT;

            $invoice = MerchantInvoice::create([
                'merchant_id' => $merchant->id,
                'invoice_number' => $this->generateMerchantInvoiceNumberAction->execute(),
                'period_start' => $validated['period_start'] ?? null,
                'period_end' => $validated['period_end'] ?? null,
                'total_cod' => $totals['total_cod'],
                'total_shipping_fees' => $totals['total_shipping_fees'],
                'total_warehouse_charges' => $totals['total_warehouse_charges'],
                'total_payable' => $totals['total_payable'],
                'status' => $status,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $createdBy,
                'issued_at' => $status === MerchantInvoiceStatus::ISSUED ? now() : null,
            ]);

            return $invoice->load(['merchant', 'createdBy']);
        });
    }
}
