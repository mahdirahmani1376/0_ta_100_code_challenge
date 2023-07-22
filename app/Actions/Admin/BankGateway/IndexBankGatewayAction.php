<?php

namespace App\Actions\Admin\BankGateway;

use App\Services\Admin\BankGateway\IndexBankGatewayService;

class IndexBankGatewayAction
{
    private IndexBankGatewayService $indexBankGatewayService;

    public function __construct(IndexBankGatewayService $indexBankGatewayService)
    {
        $this->indexBankGatewayService = $indexBankGatewayService;
    }

    public function __invoke(array $data)
    {
        return ($this->indexBankGatewayService)($data);
    }
}
