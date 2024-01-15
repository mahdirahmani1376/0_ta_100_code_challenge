<?php

namespace App\Services\BankGateway\DirectPayment;

use App\Repositories\BankGateway\Interface\DirectPaymentRepositoryInterface;

class ListDirectPaymentProvidersByProfileIdService
{
    public function __construct(private readonly DirectPaymentRepositoryInterface $directPaymentRepository)
    {
    }

    public function __invoke(int $profileId)
    {
        return $this->directPaymentRepository->listProvidersByProfileId($profileId);
    }
}
