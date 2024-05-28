<?php

namespace App\Http\Controllers;

use App\Actions\FinanceServiceHourlyReportAction;
use App\Http\Requests\ReportRequest;

class FinanceServiceHourlyReportController extends Controller
{
    public function __construct(private readonly FinanceServiceHourlyReportAction $financeServiceHourlyReportAction)
    {
        parent::__construct();
    }

    public function __invoke(ReportRequest $request)
    {
        $report = ($this->financeServiceHourlyReportAction)($request->validated());

        return response()->json(['data' => $report]);
    }
}

