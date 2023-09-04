<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Repositories\Wallet\Interface\WalletRepositoryInterface;

class StoreWalletService
{
    public function __construct(private readonly WalletRepositoryInterface $walletRepository)
    {
    }

    public function __invoke(int $clientId): Wallet
    {
        return $this->walletRepository->create([
            'client_id' => $clientId,
        ]);
    }
}
