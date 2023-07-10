<?php

namespace App\Repositories\BankGateway;

use App\Common\Repository\BaseRepository;
use App\Models\BankGateway;
use App\Repositories\BankGateway\Interface\BankGatewayInterface;

class BankGatewayRepository extends BaseRepository implements BankGatewayInterface
{
    public string $model = BankGateway::class;
}
