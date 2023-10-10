<?php

namespace App\Actions\Internal\Cloud\Invoice;

use App\Services\Invoice\IndexMyInvoiceService;

class IndexMyInvoiceControllerAction
{
    public function __construct(private readonly IndexMyInvoiceService $indexMyInvoiceService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->indexMyInvoiceService)($data);
    }
}
