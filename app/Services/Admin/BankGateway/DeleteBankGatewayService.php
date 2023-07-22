<?php

namespace App\Services\Admin\BankGateway;

use App\Models\BankGateway;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;

class DeleteBankGatewayService
{
    private BankGatewayRepositoryInterface $bankGatewayRepository;

    public function __construct(BankGatewayRepositoryInterface $bankGatewayRepository)
    {
        $this->bankGatewayRepository = $bankGatewayRepository;
    }

    public function __invoke(BankGateway $bankGateway)
    {
        return $this->bankGatewayRepository->delete($bankGateway);
    }
}
