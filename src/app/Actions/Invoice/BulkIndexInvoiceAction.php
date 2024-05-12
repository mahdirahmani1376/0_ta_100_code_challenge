<?php

namespace App\Actions\Invoice;

use App\Services\Invoice\BulkIndexInvoiceService;

class BulkIndexInvoiceAction
{
    public function __construct(private readonly BulkIndexInvoiceService $bulkIndexInvoiceService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->bulkIndexInvoiceService)($data);
    }
}