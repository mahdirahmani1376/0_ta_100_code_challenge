<?php

namespace App\Repositories\Wallet;

use App\Models\Wallet;
use App\Repositories\Base\BaseRepository;

class WalletRepository extends BaseRepository implements Interface\WalletRepositoryInterface
{
    public string $model = Wallet::class;
}
