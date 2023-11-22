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
     * @param int $profileId
     * @return ShowWalletResource
     */
    public function __invoke(int $profileId)
    {
        $wallet = ($this->showWalletAction)($profileId);

        return ShowWalletResource::make($wallet);
    }
}
