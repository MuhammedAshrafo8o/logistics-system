<?php

namespace App\Modules\Finance\Controllers;

use App\Models\Merchant;
use App\Modules\Finance\Requests\MerchantFinanceSummaryRequest;
use App\Modules\Finance\Resources\MerchantFinanceSummaryResource;
use App\Modules\Finance\Services\FinanceSummaryService;

class MerchantFinanceController
{
    public function __construct(
        private readonly FinanceSummaryService $financeSummaryService,
    ) {
    }

    public function summary(MerchantFinanceSummaryRequest $request, Merchant $merchant): MerchantFinanceSummaryResource
    {
        return new MerchantFinanceSummaryResource(
            $this->financeSummaryService->getMerchantSummary($merchant, $request->validated())
        );
    }
}
