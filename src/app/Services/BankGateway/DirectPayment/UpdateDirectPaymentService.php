<?php

namespace App\Services\BankGateway\DirectPayment;

use App\Models\DirectPayment;
use App\Repositories\BankGateway\Interface\DirectPaymentRepositoryInterface;

class UpdateDirectPaymentService
{
    public function __construct(private readonly DirectPaymentRepositoryInterface $directPaymentRepository)
    {
    }

    public function __invoke(DirectPayment $directPayment, array $data)
    {
        return $this->directPaymentRepository->update($directPayment, $data);
    }
}
