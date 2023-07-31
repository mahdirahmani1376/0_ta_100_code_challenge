<?php

namespace App\Actions\Profile\Invoice;

use App\Services\Invoice\Item\ListItemByCriteriaService;
use App\Services\Profile\Invoice\IndexInvoiceService;

class IndexInvoiceAction
{
    private IndexInvoiceService $indexInvoiceService;
    private ListItemByCriteriaService $listItemByCriteriaService;

    public function __construct(
        IndexInvoiceService       $indexInvoiceService,
        ListItemByCriteriaService $listItemByCriteriaService,
    )
    {
        $this->indexInvoiceService = $indexInvoiceService;
        $this->listItemByCriteriaService = $listItemByCriteriaService;
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
