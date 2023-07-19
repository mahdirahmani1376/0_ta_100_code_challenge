<?php

namespace App\Actions\Admin\Invoice;

use App\Models\Invoice;
use App\Services\Admin\Invoice\ManualCheckService;

class ManualCheckAction
{
    private ManualCheckService $manualCheckService;

    public function __construct(ManualCheckService $manualCheckService)
    {
        $this->manualCheckService = $manualCheckService;
    }

    public function __invoke(Invoice $invoice, int $adminId): Invoice
    {
        return ($this->manualCheckService)($invoice, $adminId);
    }
}
