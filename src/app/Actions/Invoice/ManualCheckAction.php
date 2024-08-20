<?php

namespace App\Actions\Invoice;

use App\Exceptions\SystemException\InvoiceAlreadyCheckedException;
use App\Models\Invoice;
use App\Services\Invoice\ManualCheckService;

class ManualCheckAction
{
    public function __construct(private readonly ManualCheckService $manualCheckService)
    {
    }

    public function __invoke(Invoice $invoice, int $adminId): Invoice
    {
        if ($invoice->admin_id) {
            throw InvoiceAlreadyCheckedException::make($invoice->id, $invoice->admin_id);
        }

        return ($this->manualCheckService)($invoice, $adminId);
    }
}
