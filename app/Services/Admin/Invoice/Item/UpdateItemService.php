<?php

namespace App\Services\Admin\Invoice\Item;

use App\Models\Item;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class UpdateItemService
{
    public function __construct(private readonly ItemRepositoryInterface $itemRepository)
    {
    }

    public function __invoke(Item $item, array $data): Item
    {
        return $this->itemRepository->update($item, $data);
    }
}
