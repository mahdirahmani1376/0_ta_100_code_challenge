<?php

namespace App\Actions\BankGateway;

use App\Services\BankGateway\StoreBankGatewayService;

class StoreBankGatewayAction
{
    public function __construct(private readonly StoreBankGatewayService $storeBankGatewayService)
    {
    }

    public function __invoke(array $data)
    {
        $bankGateway = ($this->storeBankGatewayService)($data);


        return $bankGateway;
    }
}
