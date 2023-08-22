<?php

namespace App\Services\Admin\Invoice\Item;

use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class FindAddCreditItemService
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(Invoice $invoice): ?Item
    {
        return $this->itemRepository->findAddCreditItem($invoice);
    }
}
