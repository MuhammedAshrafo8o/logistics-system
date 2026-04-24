<?php

namespace App\Modules\Dashboard\Controllers;

use App\Modules\Dashboard\Resources\DashboardSummaryResource;
use App\Modules\Dashboard\Services\DashboardSummaryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController
{
    public function __construct(
        private readonly DashboardSummaryService $dashboardSummaryService,
    ) {
    }

    public function summary(Request $request): DashboardSummaryResource
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'merchant_id' => [
                'nullable',
                Rule::exists('merchants', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'assigned_driver_id' => [
                'nullable',
                Rule::exists('drivers', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
        ]);

        return new DashboardSummaryResource(
            $this->dashboardSummaryService->getSummary($validated)
        );
    }
}
