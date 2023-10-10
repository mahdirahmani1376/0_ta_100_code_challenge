<?php

namespace App\Actions\Admin\Invoice;

use App\Services\Admin\Invoice\IndexInvoiceNumberService;

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
