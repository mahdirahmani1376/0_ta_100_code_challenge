<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Actions\Admin\Wallet\ShowWalletAndTransactionAction;
use App\Http\Resources\Admin\Wallet\ShowWalletAndTransactionResource;

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
