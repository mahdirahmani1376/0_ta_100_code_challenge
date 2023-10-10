<?php

namespace App\Services\Admin\BankGateway;

use App\Models\BankGateway;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;

class DeleteBankGatewayService
{
    public function __construct(private readonly BankGatewayRepositoryInterface $bankGatewayRepository)
    {
    }

    public function __invoke(BankGateway $bankGateway)
    {
        return $this->bankGatewayRepository->delete($bankGateway);
    }
}
