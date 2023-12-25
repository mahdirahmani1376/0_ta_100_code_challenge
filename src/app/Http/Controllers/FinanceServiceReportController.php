<?php

namespace App\Http\Controllers;

use App\Actions\FinanceServiceReportAction;
use App\Http\Requests\ReportRequest;
use App\Http\Resources\FinanceReport\FinanceReportResource;

class FinanceServiceReportController
{
    public function __construct(private readonly FinanceServiceReportAction $financeServiceReportAction)
    {
    }

    public function __invoke(ReportRequest $request)
    {
        $report = ($this->financeServiceReportAction)($request->validated());

        if ($request->validated('new_view', false)) {
            return $report;
        } else {
            return FinanceReportResource::make($report);
        }
    }
}

