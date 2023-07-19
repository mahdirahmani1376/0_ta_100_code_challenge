<?php

namespace App\Actions\Admin\Wallet;

use App\Models\Wallet;
use App\Services\Wallet\FindWalletByClientIdService;
use App\Services\Wallet\StoreWalletService;

class ShowWalletAction
{
    private FindWalletByClientIdService $findWalletByClientIdService;
    private StoreWalletService $storeWalletService;

    public function __construct(
        FindWalletByClientIdService $findWalletByClientIdService,
        StoreWalletService          $storeWalletService
    )
    {
        $this->findWalletByClientIdService = $findWalletByClientIdService;
        $this->storeWalletService = $storeWalletService;
    }

    public function __invoke(int $clientId): Wallet
    {
        $wallet = ($this->findWalletByClientIdService)($clientId);
        if (is_null($wallet)) {
            $wallet = ($this->storeWalletService)($clientId);
        }

        return $wallet;
    }
}
