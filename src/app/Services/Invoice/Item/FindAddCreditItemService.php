<?php

namespace App\Services\Invoice\Item;

use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class FindAddCreditItemService
{
    public function __construct(private readonly ItemRepositoryInterface $itemRepository)
    {
    }

    public function __invoke(Invoice $invoice): ?Item
    {
        return $this->itemRepository->findAddCreditItem($invoice);
    }
}
