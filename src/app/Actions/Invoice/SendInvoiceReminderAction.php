<?php

namespace App\Actions\Invoice;

use App\Exceptions\Http\BadRequestException;
use App\Jobs\OverrideInvoiceReminderJob;
use App\Models\Invoice;

class SendInvoiceReminderAction
{
    public function __invoke(Invoice $invoice): void
    {
        if (in_array($invoice->status, [
            Invoice::STATUS_PAID,
            Invoice::STATUS_DRAFT
        ])) {
            throw new BadRequestException(__('finance.invoice.NotCorrectStatus'));
        }

        OverrideInvoiceReminderJob::dispatch($invoice->id);
    }
}
