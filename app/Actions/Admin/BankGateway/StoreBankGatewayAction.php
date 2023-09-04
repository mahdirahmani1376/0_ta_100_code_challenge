<?php

namespace App\Actions\Admin\BankGateway;

use App\Services\Admin\BankGateway\StoreBankGatewayService;

class StoreBankGatewayAction
{
    public function __construct(private readonly StoreBankGatewayService $storeBankGatewayService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->storeBankGatewayService)($data);
    }
}
