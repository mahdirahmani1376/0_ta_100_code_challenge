<?php

namespace App\Actions\Admin\BankGateway;

use App\Services\Admin\BankGateway\IndexBankGatewayService;

class IndexBankGatewayAction
{
    public function __construct(private readonly IndexBankGatewayService $indexBankGatewayService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->indexBankGatewayService)($data);
    }
}
