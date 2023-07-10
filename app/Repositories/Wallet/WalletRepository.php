<?php

namespace App\Repositories\Wallet;

use App\Models\Wallet;
use App\Repositories\Base\BaseRepository;

class WalletRepository extends BaseRepository implements Interface\WalletInterface
{
    public string $model = Wallet::class;
}
