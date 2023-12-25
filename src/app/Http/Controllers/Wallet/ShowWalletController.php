<?php

namespace App\Http\Controllers\Wallet;

use App\Actions\Wallet\ShowWalletAction;
use App\Http\Resources\Wallet\ShowWalletResource;

class ShowWalletController
{
    public function __construct(private readonly ShowWalletAction $showWalletAction)
    {
    }

    /**
     * @param int $profileId
     * @return ShowWalletResource
     */
    public function __invoke(int $profileId)
    {
        $wallet = ($this->showWalletAction)($profileId, true);

        return ShowWalletResource::make($wallet);
    }
}
