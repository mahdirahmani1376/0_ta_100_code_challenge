<?php

namespace App\Actions\Invoice\InvoiceNumber;

use App\Services\Invoice\IndexInvoiceNumberService;

class IndexInvoiceNumberAction
{
    public function __construct(private readonly IndexInvoiceNumberService $indexInvoiceNumberService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->indexInvoiceNumberService)($data);
    }
}
