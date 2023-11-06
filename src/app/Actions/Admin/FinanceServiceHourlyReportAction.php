<?php

namespace App\Actions\Admin;

use App\Services\Admin\FinanceHourlyReportService;

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
