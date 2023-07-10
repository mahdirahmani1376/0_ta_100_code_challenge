<?php

namespace App\Repositories\BankGateway;

use App\Models\BankGateway;
use App\Repositories\BankGateway\Interface\BankGatewayInterface;
use App\Repositories\Base\BaseRepository;

class BankGatewayRepository extends BaseRepository implements BankGatewayInterface
{
    public string $model = BankGateway::class;
}
