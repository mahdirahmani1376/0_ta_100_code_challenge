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
     * @param int $clientId
     * @return ShowWalletAndTransactionResource
     */
    public function __invoke(int $clientId)
    {
        $wallet = ($this->showWalletAndTransactionAction)($clientId);

        return ShowWalletAndTransactionResource::make($wallet);
    }
}
