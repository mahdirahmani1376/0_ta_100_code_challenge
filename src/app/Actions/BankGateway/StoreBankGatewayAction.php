<?php

namespace App\Actions\BankGateway;

use App\Models\AdminLog;
use App\Services\BankGateway\StoreBankGatewayService;

class StoreBankGatewayAction
{
    public function __construct(private readonly StoreBankGatewayService $storeBankGatewayService)
    {
    }

    public function __invoke(array $data)
    {
        $bankGateway = ($this->storeBankGatewayService)($data);

        admin_log(AdminLog::CREATE_BANK_GATEWAY, $bankGateway, validatedData: $data);

        return $bankGateway;
    }
}
