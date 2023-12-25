<?php

namespace App\Actions;

use App\Services\Report\FinanceReportService;

class FinanceServiceReportAction
{
    public function __construct(private readonly FinanceReportService $financeReportService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->financeReportService)($data);
    }
}
