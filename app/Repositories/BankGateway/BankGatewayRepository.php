<?php

namespace App\Repositories\BankGateway;

use App\Models\BankGateway;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class BankGatewayRepository extends BaseRepository implements BankGatewayRepositoryInterface
{
    public string $model = BankGateway::class;
}
