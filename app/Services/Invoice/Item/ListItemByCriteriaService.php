<?php

namespace App\Services\Invoice\Item;

use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use Illuminate\Support\Collection;

class ListItemByCriteriaService
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(array $criteria): Collection
    {
        return $this->itemRepository->indexItemByCriteria($criteria);
    }
}
