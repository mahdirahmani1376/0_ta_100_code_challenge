<?php

namespace App\Actions\BankGateway\DirectPayment;

use App\Integrations\BankGateway\BazaarPay;
use App\Models\DirectPayment;
use App\Services\BankGateway\DirectPayment\StoreDirectPaymentService;
use App\Services\BankGateway\MakeBankGatewayProviderByNameService;

class RequestBazaarPayContractAction
{
    public function __construct(
        private readonly MakeBankGatewayProviderByNameService $makeBankGatewayProviderByNameService,
        private readonly StoreDirectPaymentService            $storeDirectPaymentService,
    )
    {
    }

    public function __invoke(array $data)
    {
        /** @var BazaarPay $bazaarPayBankGatewayProvider */
        $bazaarPayBankGatewayProvider = ($this->makeBankGatewayProviderByNameService)('bazaarPay');
        $contractToken = $bazaarPayBankGatewayProvider->initContract();

        ($this->storeDirectPaymentService)([
            'profile_id' => $data['profile_id'],
            'provider' => DirectPayment::PROVIDER_BAZAAR_PAY,
            'status' => DirectPayment::STATUS_INIT,
            'config' => [
                'contract_token' => $contractToken,
            ],
        ]);

        return $contractToken;
    }
}