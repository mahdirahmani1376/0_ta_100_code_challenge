<?php

namespace App\Http\Controllers\Internal\Cloud\Wallet;

use App\Actions\Internal\Cloud\Wallet\ShowWalletAction;
use App\Http\Resources\Internal\Cloud\Wallet\WalletResource;

class ShowWalletController
{
    public function __construct(private readonly ShowWalletAction $showWalletAction)
    {
    }

    public function __invoke(int $clientId)
    {
        $wallet = ($this->showWalletAction)($clientId);

        return WalletResource::make($wallet);
    }
}
