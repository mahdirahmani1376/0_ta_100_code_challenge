<?php

namespace App\Actions\Wallet;

use App\Models\Wallet;
use App\Services\Wallet\CalcWalletBalanceService;
use App\Services\Wallet\FindWalletByProfileIdService;
use App\Services\Wallet\StoreWalletService;

class ShowWalletAction
{
    public function __construct(
        private readonly FindWalletByProfileIdService $findWalletByProfileIdService,
        private readonly StoreWalletService           $storeWalletService,
        private readonly CalcWalletBalanceService     $calcWalletBalanceService,
    )
    {
    }

    public function __invoke(int $profileId, bool $recalculateBalance = false): Wallet
    {
        $wallet = ($this->findWalletByProfileIdService)($profileId);
        if (is_null($wallet)) {
            $wallet = ($this->storeWalletService)($profileId);
        } else if ($recalculateBalance) {
            return ($this->calcWalletBalanceService)($wallet);
        }

        return $wallet;
    }
}
