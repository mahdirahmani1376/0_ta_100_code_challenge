<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\FinanceServiceReportAction;
use App\Http\Requests\Admin\ReportRequest;
use App\Http\Resources\Admin\FinanceReport\FinanceReportResource;

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

