<?php

namespace App\Http\Controllers\Profile\Wallet;

use App\Actions\Profile\Wallet\ShowWalletAction;
use App\Http\Requests\Profile\Wallet\ShowWalletRequest;
use App\Http\Resources\Profile\Wallet\WalletResource;

class ShowWalletController
{
    public function __construct(private readonly ShowWalletAction $showWalletAction)
    {
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
