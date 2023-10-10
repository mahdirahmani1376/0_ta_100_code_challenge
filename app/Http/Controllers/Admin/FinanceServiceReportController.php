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
        return FinanceReportResource::make(($this->financeServiceReportAction)());
    }
}

