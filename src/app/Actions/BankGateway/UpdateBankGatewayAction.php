<?php

namespace App\Actions\BankGateway;

use App\Models\BankGateway;
use App\Services\BankGateway\UpdateBankGatewayService;

class UpdateBankGatewayAction
{
    public function __construct(private readonly UpdateBankGatewayService $updateBankGatewayService)
    {
    }

    public function __invoke(BankGateway $bankGateway, array $data): BankGateway
    {
        $oldState = $bankGateway->toArray();
        $bankGateway = ($this->updateBankGatewayService)($bankGateway, $data);


        return $bankGateway;
    }
}
