<?php

namespace App\Http\Controllers\Profile\Wallet;

use App\Actions\Profile\Wallet\ShowWalletAction;
use App\Http\Requests\Profile\Wallet\ShowWalletRequest;
use App\Http\Resources\Profile\Wallet\WalletResource;

class ShowWalletController
{
    private ShowWalletAction $showWalletAction;

    public function __construct(ShowWalletAction $showWalletAction)
    {
        $this->showWalletAction = $showWalletAction;
    }

    /**
     * @param ShowWalletRequest $request
     * @return WalletResource
     */
    public function __invoke(ShowWalletRequest $request)
    {
        $wallet = ($this->showWalletAction)($request->validated('client_id'));

        return WalletResource::make($wallet);
    }
}
