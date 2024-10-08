<?php

namespace App\Actions\BankGateway;

use App\Models\BankGateway;
use App\Services\BankGateway\DeleteBankGatewayService;

class DeleteBankGatewayAction
{
    public function __construct(private readonly DeleteBankGatewayService $deleteBankGatewayService)
    {
    }

    public function __invoke(BankGateway $bankGateway)
    {

        return ($this->deleteBankGatewayService)($bankGateway);
    }
}
