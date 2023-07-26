<?php

namespace App\Actions\Public\BankGateway;

use App\Services\BankGateway\IndexBankGatewayService;

class IndexBankGatewayAction
{
    private IndexBankGatewayService $indexBankGatewayService;

    public function __construct(IndexBankGatewayService $indexBankGatewayService)
    {
        $this->indexBankGatewayService = $indexBankGatewayService;
    }

    public function __invoke()
    {
        return ($this->indexBankGatewayService)();
    }
}
