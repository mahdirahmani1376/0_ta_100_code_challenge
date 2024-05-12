<?php

namespace App\Services\BankGateway\DirectPayment;

use App\Repositories\BankGateway\Interface\DirectPaymentRepositoryInterface;

class StoreDirectPaymentService
{
    public function __construct(private readonly DirectPaymentRepositoryInterface $directPaymentRepository)
    {
    }

    public function __invoke(array $data)
    {
        return $this->directPaymentRepository->create($data);
    }
}
