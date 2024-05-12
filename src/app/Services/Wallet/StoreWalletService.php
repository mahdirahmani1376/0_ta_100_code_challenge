<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Repositories\Wallet\Interface\WalletRepositoryInterface;

class StoreWalletService
{
    public function __construct(private readonly WalletRepositoryInterface $walletRepository)
    {
    }

    public function __invoke(int $profileId): Wallet
    {
        return $this->walletRepository->create([
            'profile_id' => $profileId,
            'name' => Wallet::WALLET_DEFAULT_NAME,
            'balance' => 0,
            'is_active' => true,
        ]);
    }
}
