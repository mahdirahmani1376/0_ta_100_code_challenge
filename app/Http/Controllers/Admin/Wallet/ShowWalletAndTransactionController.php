<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Actions\Admin\Wallet\ShowWalletAndTransactionAction;
use App\Http\Resources\Admin\Wallet\ShowWalletAndTransactionResource;

class ShowWalletAndTransactionController
{
    private ShowWalletAndTransactionAction $showWalletAndTransactionAction;

    public function __construct(ShowWalletAndTransactionAction $showWalletAndTransactionAction)
    {
        $this->showWalletAndTransactionAction = $showWalletAndTransactionAction;
    }

    public function __invoke(int $clientId)
    {
        $wallet = ($this->showWalletAndTransactionAction)($clientId);

        return ShowWalletAndTransactionResource::make($wallet);
    }
}
