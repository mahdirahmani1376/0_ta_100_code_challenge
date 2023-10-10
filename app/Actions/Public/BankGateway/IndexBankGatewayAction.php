<?php

namespace App\Actions\Public\BankGateway;

use App\Services\BankGateway\IndexBankGatewayService;

class IndexBankGatewayAction
{
    public function __construct(private readonly IndexBankGatewayService $indexBankGatewayService)
    {
    }

    public function __invoke()
    {
        return ($this->indexBankGatewayService)();
    }
}
