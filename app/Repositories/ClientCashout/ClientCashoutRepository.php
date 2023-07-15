<?php

namespace App\Repositories\ClientCashout;

use App\Models\ClientCashout;
use App\Repositories\Base\BaseRepository;
use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;

class ClientCashoutRepository extends BaseRepository implements ClientCashoutRepositoryInterface
{
    public string $model = ClientCashout::class;
}
