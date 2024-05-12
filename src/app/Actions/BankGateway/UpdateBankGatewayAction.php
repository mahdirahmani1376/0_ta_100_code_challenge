<?php

namespace App\Actions\BankGateway;

use App\Models\AdminLog;
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

        admin_log(AdminLog::UPDATE_BANK_GATEWAY, $bankGateway, $bankGateway->getChanges(), $oldState, $data);

        return $bankGateway;
    }
}
