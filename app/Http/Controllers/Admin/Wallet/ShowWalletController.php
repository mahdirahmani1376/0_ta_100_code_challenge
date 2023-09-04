<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Actions\Admin\Wallet\ShowWalletAction;
use App\Http\Resources\Admin\Wallet\ShowWalletResource;

class ShowWalletController
{
    public function __construct(private readonly ShowWalletAction $showWalletAction)
    {
    }

    /**
     * @param int $clientId
     * @return ShowWalletResource
     */
    public function __invoke(int $clientId)
    {
        $wallet = ($this->showWalletAction)($clientId);

        return ShowWalletResource::make($wallet);
    }
}
