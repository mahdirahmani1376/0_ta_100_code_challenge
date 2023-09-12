<?php

namespace App\Actions\Admin\BankGateway;

use App\Models\AdminLog;
use App\Services\Admin\BankGateway\StoreBankGatewayService;

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
