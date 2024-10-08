<?php

namespace App\Http\Controllers\Wallet;

use App\Actions\Wallet\ShowWalletAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Wallet\ShowWalletResource;

class ShowWalletController extends Controller
{
    public function __construct(private readonly ShowWalletAction $showWalletAction)
    {
        parent::__construct();
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
