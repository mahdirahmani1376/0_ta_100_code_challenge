<?php

namespace App\Actions\ClientCashout;

use App\Exceptions\SystemException\AmountIsMoreThanWalletBalanceException;
use App\Exceptions\SystemException\AmountMustBeGreaterThanZeroException;
use App\Models\ClientCashout;
use App\Services\ClientCashout\StoreClientCashoutService;
use App\Services\Wallet\FindWalletByProfileIdService;

class StoreClientCashoutAction
{
    public function __construct(
        private readonly StoreClientCashoutService $clientCashoutService,
        private readonly FindWalletByProfileIdService $findWalletByProfileIdService
    )
    {
    }

    public function __invoke(array $data)
    {
        $amount = data_get($data,'amount');
        if ($amount <= 0) {
            throw AmountMustBeGreaterThanZeroException::make();
        }

        $wallet = ($this->findWalletByProfileIdService)(data_get($data,'profile_id'));
        if (data_get($data,'amount') > $wallet->balance){
            throw AmountIsMoreThanWalletBalanceException::make();
        }

        $data['status'] = $data['status'] ?? ClientCashout::STATUS_PENDING;
        if (empty($data['admin_id'])) {
            $data['status'] = ClientCashout::STATUS_PENDING;
        }
        $clientCashout = ($this->clientCashoutService)($data);


        return $clientCashout;
    }
}
