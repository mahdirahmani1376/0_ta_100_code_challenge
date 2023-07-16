<?php

namespace App\Repositories\Invoice;

use App\Models\Item;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    public string $model = Item::class;

    /**
     * @throws BindingResolutionException
     */
    public function indexItemByCriteria(array $criteria, int $limit = 100): Collection
    {
        if (empty(array_intersect(array_keys($criteria), [
            'search',
        ]))) {
            return collect();
        }

        $query = self::newQuery();
        if (!empty($criteria['search'])) {
            $query->where('description', 'LIKE', "%" . $criteria['search'] . "%");
        }

        $query->limit($limit);

        return $query->get();
    }
}
