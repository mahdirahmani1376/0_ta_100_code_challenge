<?php

namespace App\Actions\Profile\Invoice;

use App\Services\Invoice\Item\ListItemByCriteriaService;
use App\Services\Profile\Invoice\IndexInvoiceService;

class IndexInvoiceAction
{
    public function __construct(
        private readonly IndexInvoiceService       $indexInvoiceService,
        private readonly ListItemByCriteriaService $listItemByCriteriaService,
    )
    {
    }

    public function __invoke(array $data)
    {
        $items = ($this->listItemByCriteriaService)($data);
        if ($items->isNotEmpty()) {
            $data['item_invoice_ids'] = $items->pluck('invoice_id')->unique()->toArray();
        }

        return ($this->indexInvoiceService)($data);
    }
}
