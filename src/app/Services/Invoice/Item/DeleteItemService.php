<?php

namespace App\Services\Invoice\Item;

use App\Models\Item;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class DeleteItemService
{
    public function __construct(private readonly ItemRepositoryInterface $itemRepository)
    {
    }

    public function __invoke(Item $item)
    {
        return $this->itemRepository->delete($item);
    }
}
