<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\FinanceServiceReportAction;
use App\Http\Resources\Admin\FinanceReport\FinanceReportResource;

class FinanceServiceReportController
{
    public function __construct(private readonly FinanceServiceReportAction $financeServiceReportAction)
    {
    }

    public function __invoke()
    {
        $view = request('view', 0);
        $report = ($this->financeServiceReportAction)($view);

        switch ($view) {
            case 1:
                return  $report;
            default:
                return FinanceReportResource::make($report);
        }
    }
}

