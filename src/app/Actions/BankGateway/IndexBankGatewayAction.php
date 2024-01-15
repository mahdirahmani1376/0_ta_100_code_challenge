<?php

namespace App\Actions\BankGateway;

use App\Models\BankGateway;
use App\Services\BankGateway\DirectPayment\ListDirectPaymentProvidersByProfileIdService;
use App\Services\BankGateway\IndexBankGatewayService;

class IndexBankGatewayAction
{
    public function __construct(
        private readonly IndexBankGatewayService                      $indexBankGatewayService,
        private readonly ListDirectPaymentProvidersByProfileIdService $listDirectPaymentProvidersByProfileIdService,
    )
    {
    }

    public function __invoke(array $data)
    {
        if (empty($data['admin_id'])) {
            $data['status'] = BankGateway::STATUS_ACTIVE;
        }
        if (!empty($data['profile_id'])) {
            $providers = ($this->listDirectPaymentProvidersByProfileIdService)($data['profile_id']);
            $data['direct_payment_providers'] = $providers;
        }

        return ($this->indexBankGatewayService)($data);
    }
}
