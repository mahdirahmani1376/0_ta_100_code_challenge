<?php

namespace App\Repositories\Invoice\Interface;

use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Support\Collection;

interface ItemRepositoryInterface extends EloquentRepositoryInterface
{
    public function indexItemByCriteria(array $criteria, int $limit = 100): Collection;
}
