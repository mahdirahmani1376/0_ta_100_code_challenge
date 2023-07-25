<?php

namespace App\Actions\Admin\BankGateway;

use App\Services\Admin\BankGateway\StoreBankGatewayService;

class StoreBankGatewayAction
{
    private StoreBankGatewayService $storeBankGatewayService;

    public function __construct(StoreBankGatewayService $storeBankGatewayService)
    {
        $this->storeBankGatewayService = $storeBankGatewayService;
    }

    public function __invoke(array $data)
    {
        return ($this->storeBankGatewayService)($data);
    }
}
