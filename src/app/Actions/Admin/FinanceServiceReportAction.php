<?php

namespace App\Actions\Admin;

use App\Services\Admin\FinanceReportService;

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
