<?php

namespace App\Actions\Admin\BankGateway;

use App\Models\BankGateway;
use App\Services\Admin\BankGateway\DeleteBankGatewayService;
use App\Services\Admin\BankGateway\UpdateBankGatewayService;

class DeleteBankGatewayAction
{
    private DeleteBankGatewayService $deleteBankGatewayService;

    public function __construct(DeleteBankGatewayService $deleteBankGatewayService)
    {
        $this->deleteBankGatewayService = $deleteBankGatewayService;
    }

    public function __invoke(BankGateway $bankGateway)
    {
        return ($this->deleteBankGatewayService)($bankGateway);
    }
}
