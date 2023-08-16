<?php

namespace App\Services\Admin\Invoice\Item;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class ReAssignItemToInvoiceService
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(Invoice $invoice, array $itemIds)
    {
        return $this->itemRepository->reAssignItemsToInvoice($invoice, $itemIds);
    }
}
