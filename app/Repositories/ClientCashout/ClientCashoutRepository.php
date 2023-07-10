<?php

namespace App\Repositories\ClientCashout;

use App\Models\ClientCashout;
use App\Repositories\Base\BaseRepository;
use App\Repositories\ClientCashout\Interface\ClientCashoutInterface;

class ClientCashoutRepository extends BaseRepository implements ClientCashoutInterface
{
    public string $model = ClientCashout::class;
}
