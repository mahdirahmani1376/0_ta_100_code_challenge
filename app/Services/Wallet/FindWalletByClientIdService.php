<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Repositories\Wallet\Interface\WalletRepositoryInterface;

class FindWalletByClientIdService
{
    public function __construct(private readonly WalletRepositoryInterface $walletRepository)
    {
    }

    public function __invoke(int $clientId): ?Wallet
    {
        return $this->walletRepository->findByClientId($clientId);
    }
}
