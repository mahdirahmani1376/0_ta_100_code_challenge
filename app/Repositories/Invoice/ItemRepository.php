<?php

namespace App\Repositories\Invoice;

use App\Common\Repository\BaseRepository;
use App\Models\Item;
use App\Repositories\Invoice\Interface\ItemInterface;

class ItemRepository extends BaseRepository implements ItemInterface
{
    public string $model = Item::class;
}
