<?php

namespace App\Services\BankGateway\DirectPayment;

use App\Models\DirectPayment;
use App\Repositories\BankGateway\Interface\DirectPaymentRepositoryInterface;

class FindDirectPaymentByProfileIdService
{
    public function __construct(private readonly DirectPaymentRepositoryInterface $directPaymentRepository)
    {
    }

    public function __invoke(int $profileId): ?DirectPayment
    {
        return $this->directPaymentRepository->findByProfileId($profileId);
    }
}
