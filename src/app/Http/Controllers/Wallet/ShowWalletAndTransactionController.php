<?php

namespace App\Http\Controllers\Wallet;

use App\Actions\Wallet\ShowWalletAndTransactionAction;
use App\Http\Resources\Wallet\ShowWalletAndTransactionResource;

class ShowWalletAndTransactionController
{
    public function __construct(private readonly ShowWalletAndTransactionAction $showWalletAndTransactionAction)
    {
    }

    /**
     * @param int $profileId
     * @return ShowWalletAndTransactionResource
     */
    public function __invoke(int $profileId)
    {
        $wallet = ($this->showWalletAndTransactionAction)($profileId);

        return ShowWalletAndTransactionResource::make($wallet);
    }
}
