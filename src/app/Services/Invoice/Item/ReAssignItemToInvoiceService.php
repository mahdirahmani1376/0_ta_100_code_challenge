<?php

namespace App\Services\Invoice\Item;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class ReAssignItemToInvoiceService
{
    public function __construct(private readonly ItemRepositoryInterface $itemRepository)
    {
    }

    public function __invoke(Invoice $invoice, array $itemIds)
    {
        return $this->itemRepository->reAssignItemsToInvoice($invoice, $itemIds);
    }
}
