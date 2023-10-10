<?php

namespace App\Actions\Admin;

use App\Services\Admin\FinanceReportService;

class FinanceServiceReportAction
{
    public function __construct(private readonly FinanceReportService $financeReportService)
    {
    }

    public function __invoke()
    {
        return ($this->financeReportService)();
    }

}
