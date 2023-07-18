<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\ChangeInvoiceStatusAction;
use App\Http\Requests\Admin\Invoice\ChangeInvoiceStatusRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;
use App\Models\Invoice;

class ChangeInvoiceStatusController
{
    private ChangeInvoiceStatusAction $changeInvoiceStatusAction;

    public function __construct(ChangeInvoiceStatusAction $changeInvoiceStatusAction)
    {
        $this->changeInvoiceStatusAction = $changeInvoiceStatusAction;
    }

    public function __invoke(Invoice $invoice, ChangeInvoiceStatusRequest $request)
    {
        $invoice = ($this->changeInvoiceStatusAction)($invoice, $request->validated('status'));

        return InvoiceResource::make($invoice);
    }
}
