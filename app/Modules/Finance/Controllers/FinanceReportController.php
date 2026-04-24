<?php

namespace App\Modules\Finance\Controllers;

use App\Modules\Finance\Requests\CompanyProfitSummaryRequest;
use App\Modules\Finance\Requests\ReconciliationSummaryRequest;
use App\Modules\Finance\Resources\CompanyProfitSummaryResource;
use App\Modules\Finance\Resources\ReconciliationSummaryResource;
use App\Modules\Finance\Services\FinanceSummaryService;

class FinanceReportController
{
    public function __construct(
        private readonly FinanceSummaryService $financeSummaryService,
    ) {
    }

    public function companyProfitSummary(CompanyProfitSummaryRequest $request): CompanyProfitSummaryResource
    {
        return new CompanyProfitSummaryResource(
            $this->financeSummaryService->getCompanyProfitSummary($request->validated())
        );
    }

    public function reconciliationSummary(ReconciliationSummaryRequest $request): ReconciliationSummaryResource
    {
        return new ReconciliationSummaryResource(
            $this->financeSummaryService->getReconciliationSummary($request->validated())
        );
    }
}
