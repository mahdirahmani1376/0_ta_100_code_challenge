<?php

namespace App\Actions\Admin\BankGateway;

use App\Models\BankGateway;
use App\Services\Admin\BankGateway\UpdateBankGatewayService;

class UpdateBankGatewayAction
{
    private UpdateBankGatewayService $updateBankGatewayService;

    public function __construct(UpdateBankGatewayService $updateBankGatewayService)
    {
        $this->updateBankGatewayService = $updateBankGatewayService;
    }

    public function __invoke(BankGateway $bankGateway, array $data): BankGateway
    {
        return ($this->updateBankGatewayService)($bankGateway, $data);
    }
}
