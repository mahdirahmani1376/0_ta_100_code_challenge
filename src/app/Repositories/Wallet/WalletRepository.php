<?php

namespace App\Repositories\Wallet;

use App\Models\Wallet;
use App\Repositories\Base\BaseRepository;

class WalletRepository extends BaseRepository implements Interface\WalletRepositoryInterface
{
    public string $model = Wallet::class;

    public function findByClientId(int $clientId): Wallet|null
    {
        return self::newQuery()
            ->where('client_id', $clientId)
            ->first();
    }

    public function reportSum(): array
    {
        return [
            'total_wallet' => self::newQuery()->where('is_active', true)->sum('balance')
        ];
    }
}
