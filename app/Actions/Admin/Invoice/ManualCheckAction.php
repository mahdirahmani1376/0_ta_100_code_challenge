<?php

namespace App\Actions\Admin\Invoice;

use App\Models\AdminLog;
use App\Models\Invoice;
use App\Services\Admin\Invoice\ManualCheckService;

class ManualCheckAction
{
    public function __construct(private readonly ManualCheckService $manualCheckService)
    {
    }

    public function __invoke(Invoice $invoice, int $adminId): Invoice
    {
        $oldState = $invoice->toArray();

        $invoice = ($this->manualCheckService)($invoice, $adminId);

        admin_log(AdminLog::MANUAL_CHECK_INVOICE, $invoice, $invoice->getChanges(), $oldState, ['admin_id' => $adminId]);

        return $invoice;
    }
}
