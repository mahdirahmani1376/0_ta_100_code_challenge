<?php

namespace App\Repositories\Wallet\Interface;

use App\Models\Wallet;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;

interface WalletRepositoryInterface extends EloquentRepositoryInterface
{
    public function findByClientId(int $clientId): Wallet|null;
}
