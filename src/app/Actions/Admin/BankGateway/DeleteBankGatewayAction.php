<?php

namespace App\Actions\Admin\BankGateway;

use App\Models\AdminLog;
use App\Models\BankGateway;
use App\Services\Admin\BankGateway\DeleteBankGatewayService;

class DeleteBankGatewayAction
{
    public function __construct(private readonly DeleteBankGatewayService $deleteBankGatewayService)
    {
    }

    public function __invoke(BankGateway $bankGateway)
    {
        admin_log(AdminLog::DELETE_BANK_GATEWAY, $bankGateway);

        return ($this->deleteBankGatewayService)($bankGateway);
    }
}
