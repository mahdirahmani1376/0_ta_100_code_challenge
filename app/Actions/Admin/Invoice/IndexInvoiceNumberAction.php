<?php

namespace App\Actions\Admin\Invoice;

use App\Services\Admin\Invoice\IndexInvoiceNumberService;

class IndexInvoiceNumberAction
{
    private IndexInvoiceNumberService $indexInvoiceNumberService;

    public function __construct(IndexInvoiceNumberService $indexInvoiceNumberService)
    {
        $this->indexInvoiceNumberService = $indexInvoiceNumberService;
    }

    public function __invoke(array $data)
    {
        return ($this->indexInvoiceNumberService)($data);
    }
}
