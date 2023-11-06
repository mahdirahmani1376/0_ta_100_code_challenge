<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\FinanceServiceHourlyReportAction;
use App\Actions\Admin\FinanceServiceReportAction;
use App\Http\Requests\Admin\ReportRequest;

class FinanceServiceHourlyReportController
{
    public function __construct(private readonly FinanceServiceHourlyReportAction $financeServiceHourlyReportAction)
    {
    }

    public function __invoke(ReportRequest $request)
    {
        $report = ($this->financeServiceHourlyReportAction)($request->validated());

        return response()->json(['data' => $report]);
    }
}

