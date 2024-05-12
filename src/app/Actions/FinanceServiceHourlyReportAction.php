<?php

namespace App\Actions;

use App\Services\Report\FinanceHourlyReportService;

class FinanceServiceHourlyReportAction
{
    public function __construct(private readonly FinanceHourlyReportService $financeHourlyReportService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->financeHourlyReportService)($data);
    }
}
