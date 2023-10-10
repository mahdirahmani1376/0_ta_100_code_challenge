<?php

namespace App\Actions\Internal\Cloud\Invoice;

use App\Services\Invoice\IndexInvoiceService;

class IndexInvoiceAction
{
    public function __construct(private readonly IndexInvoiceService $indexInvoiceService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->indexInvoiceService)($data);
    }
}
