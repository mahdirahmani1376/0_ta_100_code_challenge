<?php

namespace App\Http\Controllers\Wallet;

use App\Actions\Wallet\ShowWalletAndTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Wallet\ShowWalletAndTransactionResource;

class ShowWalletAndTransactionController extends Controller
{
    public function __construct(private readonly ShowWalletAndTransactionAction $showWalletAndTransactionAction)
    {
        parent::__construct();
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
