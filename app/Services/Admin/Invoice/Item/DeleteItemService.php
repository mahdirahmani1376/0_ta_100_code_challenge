<?php

namespace App\Services\Admin\Invoice\Item;

use App\Models\Item;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class DeleteItemService
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(Item $item)
    {
        return $this->itemRepository->delete($item);
    }
}
