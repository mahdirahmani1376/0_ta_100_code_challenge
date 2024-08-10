<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Services\Invoice\ManualCheckService;

class ManualCheckAction
{
    public function __construct(private readonly ManualCheckService $manualCheckService)
    {
    }

    public function __invoke(Invoice $invoice, int $adminId): Invoice
    {
        return ($this->manualCheckService)($invoice, $adminId);
    }
}
