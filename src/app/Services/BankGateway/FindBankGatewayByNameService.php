<?php

namespace App\Services\BankGateway;

use App\Models\BankGateway;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;

class FindBankGatewayByNameService
{
    public function __construct(private readonly BankGatewayRepositoryInterface $bankGatewayRepository)
    {
    }

    public function __invoke(string $bankGatewayName): ?BankGateway
    {
        return $this->bankGatewayRepository->findByName($bankGatewayName);
    }
}
