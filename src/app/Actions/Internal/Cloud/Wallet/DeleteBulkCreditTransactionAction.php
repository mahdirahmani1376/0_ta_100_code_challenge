<?php

namespace App\Actions\Internal\Cloud\Wallet;

use App\Services\Wallet\DeleteBulkCreditTransactionService;

class DeleteBulkCreditTransactionAction
{
    public function __construct(private readonly DeleteBulkCreditTransactionService $deleteBulkCreditTransactionService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->deleteBulkCreditTransactionService)($data['ids']);
    }
}
