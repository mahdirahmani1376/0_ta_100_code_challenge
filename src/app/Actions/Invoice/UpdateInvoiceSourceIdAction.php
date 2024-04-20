<?php

namespace App\Actions\Invoice;

use App\Models\AdminLog;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class UpdateInvoiceSourceIdAction
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository
    )
    {
    }

    public function __invoke(array $data)
    {
        $invoice = $this->invoiceRepository->find($data['invoice_id']);
        $oldState = $invoice->toArray();

        $invoice = $this->invoiceRepository->update($invoice,['source_invoice' => $data['source_invoice']],['source_invoice']);

        admin_log(AdminLog::UPDATE_INVOICE, $invoice, $invoice->getChanges(), $oldState, $data);

        return $invoice;
    }
}
