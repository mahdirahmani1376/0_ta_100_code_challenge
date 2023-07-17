<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Repositories\Wallet\Interface\WalletRepositoryInterface;

class FindWalletByClientIdService
{
    private WalletRepositoryInterface $walletRepository;

    public function __construct(WalletRepositoryInterface $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    public function __invoke(int $clientId): Wallet|null
    {
        $wallet = $this->walletRepository->findByClientId($clientId);
        if (is_null($wallet)) {
            return $this->walletRepository->create([
                'client_id' => $clientId,
            ]);
        }

        return $wallet;
    }
}
