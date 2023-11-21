<?php

namespace App\Actions\Admin\Wallet;

use App\Models\Wallet;
use App\Services\Wallet\FindWalletByProfileIdService;
use App\Services\Wallet\StoreWalletService;

class ShowWalletAction
{
    public function __construct(
        private readonly FindWalletByProfileIdService $findWalletByProfileIdService,
        private readonly StoreWalletService           $storeWalletService
    )
    {
    }

    public function __invoke(int $profileId): Wallet
    {
        $wallet = ($this->findWalletByProfileIdService)($profileId);
        if (is_null($wallet)) {
            $wallet = ($this->storeWalletService)($profileId);
        }

        return $wallet;
    }
}
