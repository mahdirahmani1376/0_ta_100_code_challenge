<?php

namespace App\Services\BankGateway;

use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;

class StoreBankGatewayService
{
    public function __construct(private readonly BankGatewayRepositoryInterface $bankGatewayRepository)
    {
    }

    public function __invoke(array $data)
    {
        return $this->bankGatewayRepository->create($data, [
            'name', 'name_fa', 'config', 'status', 'order', 'is_direct_payment_provider',
        ]);
    }
}
