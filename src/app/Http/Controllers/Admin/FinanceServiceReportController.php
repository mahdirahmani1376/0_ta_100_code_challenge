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

        switch ($request->validated('view')) {
            case 1:
                return  $report;
            default:
                return FinanceReportResource::make($report);
        }
    }
}

