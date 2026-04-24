<?php

namespace App\Modules\Finance\Actions;

use App\Modules\Finance\Models\MerchantInvoice;

class GenerateMerchantInvoiceNumberAction
{
    public function execute(): string
    {
        $datePart = now()->format('Ymd');
        $prefix = 'INV-'.$datePart.'-';

        $latestInvoiceNumber = MerchantInvoice::withTrashed()
            ->where('invoice_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $nextSequence = $latestInvoiceNumber === null
            ? 1
            : ((int) substr($latestInvoiceNumber, -6)) + 1;

        return $prefix.str_pad((string) $nextSequence, 6, '0', STR_PAD_LEFT);
    }
}
