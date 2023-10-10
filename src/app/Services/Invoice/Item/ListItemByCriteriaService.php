<?php

namespace App\Services\Invoice\Item;

use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use Illuminate\Support\Collection;

class ListItemByCriteriaService
{
    public function __construct(private readonly ItemRepositoryInterface $itemRepository)
    {
    }

    public function __invoke(array $criteria): Collection
    {
        return $this->itemRepository->indexItemByCriteria($criteria);
    }
}
