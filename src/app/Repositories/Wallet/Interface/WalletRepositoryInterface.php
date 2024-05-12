<?php

namespace App\Repositories\Wallet\Interface;

use App\Models\Wallet;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;

interface WalletRepositoryInterface extends EloquentRepositoryInterface
{
    public function findByProfileId(int $profileId): Wallet|null;

    public function reportSum(): array;
}
