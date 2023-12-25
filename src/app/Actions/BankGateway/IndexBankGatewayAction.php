<?php

namespace App\Actions\BankGateway;

use App\Models\BankGateway;
use App\Services\BankGateway\IndexBankGatewayService;

class IndexBankGatewayAction
{
    public function __construct(private readonly IndexBankGatewayService $indexBankGatewayService)
    {
    }

    public function __invoke(array $data)
    {
        if (empty($data['admin_id'])) {
            $data['status'] = BankGateway::STATUS_ACTIVE;
        }

        return ($this->indexBankGatewayService)($data);
    }
}
