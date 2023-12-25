<?php

namespace App\Repositories\BankGateway\Interface;

use App\Models\BankGateway;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;

interface BankGatewayRepositoryInterface extends EloquentRepositoryInterface
{
    public function findByName(string $name): ?BankGateway;
}
