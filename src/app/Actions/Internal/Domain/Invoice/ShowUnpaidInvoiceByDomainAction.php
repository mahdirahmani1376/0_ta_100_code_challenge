<?php

namespace App\Actions\Internal\Domain\Invoice;

use App\Models\Invoice;
use App\Models\Item;
use App\Services\Invoice\IndexInvoiceService;

class ShowUnpaidInvoiceByDomainAction
{
    public function __construct(private readonly IndexInvoiceService $indexInvoiceService)
    {
    }

    public function __invoke($domainId)
    {
        return ($this->indexInvoiceService)([
            'invoiceable_id' => $domainId,
            'invoiceable_type' => Item::TYPE_DOMAIN_SERVICE,
            'status' => Invoice::STATUS_UNPAID
        ])->first();
    }
}
