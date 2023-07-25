<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Actions\Admin\Wallet\ShowWalletAction;
use App\Http\Resources\Admin\Wallet\ShowWalletResource;

class ShowWalletController
{
    private ShowWalletAction $showWalletAction;

    public function __construct(ShowWalletAction $showWalletAction)
    {
        $this->showWalletAction = $showWalletAction;
    }

    public function __invoke(int $clientId)
    {
        $wallet = ($this->showWalletAction)($clientId);

        return ShowWalletResource::make($wallet);
    }
}
