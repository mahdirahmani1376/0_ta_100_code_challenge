<?php

namespace App\Services\Admin\Invoice\Item;

use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class UpdateItemService
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(Item $item, array $data): Item
    {
        return $this->itemRepository->update($item, $data);
    }
}
