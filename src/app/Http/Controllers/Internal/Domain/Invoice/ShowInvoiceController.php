<?php

namespace App\Http\Controllers\Internal\Domain\Invoice;

use App\Http\Resources\Internal\Domain\Invoice\InvoiceResource;
use App\Models\Invoice;

class  ShowInvoiceController
{
    public function __invoke(Invoice $invoice)
    {
        return InvoiceResource::make($invoice);
    }
}
