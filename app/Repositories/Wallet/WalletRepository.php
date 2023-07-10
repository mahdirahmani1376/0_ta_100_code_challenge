<?php

namespace App\Repositories\Wallet;

use App\Common\Repository\BaseRepository;
use App\Models\Wallet;

class WalletRepository extends BaseRepository implements Interface\WalletInterface
{
    public string $model = Wallet::class;
}
