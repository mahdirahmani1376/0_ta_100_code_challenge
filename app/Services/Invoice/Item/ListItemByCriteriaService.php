<?php

namespace App\Services\Invoice\Item;

use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ListItemByCriteriaService
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(array $criteria): Collection
    {
        $query = $this->itemRepository->newQuery();
        if (!empty($criteria['keyword'])) {
            $query->where('description', 'LIKE', "%" . $criteria['keyword'] . "%");
        }
        if (!empty($criteria['from_date'])) {
            $query->whereDate('from_date', '>=', $criteria['from_date']);
        }
        if (!empty($criteria['to_date'])) {
            $query->whereDate('to_date', '<=', $criteria['to_date']);
        }

        return $query->get();
    }
}
