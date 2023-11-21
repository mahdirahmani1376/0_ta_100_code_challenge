<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Repositories\Wallet\Interface\WalletRepositoryInterface;

class FindWalletByProfileIdService
{
    public function __construct(private readonly WalletRepositoryInterface $walletRepository)
    {
    }

    public function __invoke(int $profileId): ?Wallet
    {
        return $this->walletRepository->findByProfileId($profileId);
    }
}
