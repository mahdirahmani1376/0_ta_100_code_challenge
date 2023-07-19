<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Repositories\Wallet\Interface\WalletRepositoryInterface;

class StoreWalletService
{
    private WalletRepositoryInterface $walletRepository;

    public function __construct(WalletRepositoryInterface $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    public function __invoke(int $clientId): Wallet
    {
        return $this->walletRepository->create([
            'client_id' => $clientId,
        ]);
    }
}
