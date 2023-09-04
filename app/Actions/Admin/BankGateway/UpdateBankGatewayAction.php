<?php

namespace App\Actions\Admin\BankGateway;

use App\Models\BankGateway;
use App\Services\Admin\BankGateway\UpdateBankGatewayService;

class UpdateBankGatewayAction
{
    public function __construct(private readonly UpdateBankGatewayService $updateBankGatewayService)
    {
    }

    public function __invoke(BankGateway $bankGateway, array $data): BankGateway
    {
        return ($this->updateBankGatewayService)($bankGateway, $data);
    }
}
