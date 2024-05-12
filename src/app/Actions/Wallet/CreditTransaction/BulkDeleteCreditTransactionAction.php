<?php

namespace App\Actions\Wallet\CreditTransaction;

use App\Services\Wallet\BulkDeleteCreditTransactionService;
use App\Services\Wallet\SumAmountOfCreditTransactionService;

class BulkDeleteCreditTransactionAction
{
    public function __construct(
        private readonly SumAmountOfCreditTransactionService $sumAmountOfCreditTransactionService,
        private readonly BulkDeleteCreditTransactionService  $bulkDeleteCreditTransactionService
    )
    {
    }

    public function __invoke(array $data): array
    {
        return [
            'sum' => ($this->sumAmountOfCreditTransactionService)($data['credit_transaction_ids']),
            'count' => ($this->bulkDeleteCreditTransactionService)($data['credit_transaction_ids']),
        ];
    }
}
