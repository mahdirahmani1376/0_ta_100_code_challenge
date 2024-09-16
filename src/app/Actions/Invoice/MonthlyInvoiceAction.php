<?php

namespace App\Actions\Invoice;

use App\Services\Invoice\MonthlyInvoiceService;

class MonthlyInvoiceAction
{
    public function __construct(
        private readonly MonthlyInvoiceService $monthlyInvoiceService
    )
    {
    }

    public function __invoke(array $data)
    {
        return ($this->monthlyInvoiceService)($data);
    }
}
