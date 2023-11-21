<?php

namespace App\Actions\Internal\Cloud\Invoice;

use App\Actions\Admin\Wallet\StoreCreditTransactionAction;
use App\Models\Invoice;
use App\Services\Wallet\DeleteBulkCreditTransactionService;
use App\Services\Wallet\SumAmountOfCreditTransactionService;

class MonthlyStoreInvoiceAction
{
    public function __construct(
        private readonly StoreInvoiceAction                  $storeInvoiceAction,
        private readonly SumAmountOfCreditTransactionService $sumAmountOfCreditTransactionService,
        private readonly DeleteBulkCreditTransactionService  $deleteBulkCreditTransactionService,
        private readonly StoreCreditTransactionAction        $storeCreditTransactionAction
    )
    {
    }

    public function __invoke(array $data)
    {
        $sum = abs(($this->sumAmountOfCreditTransactionService)($data['credit_transaction_ids']));
        ($this->deleteBulkCreditTransactionService)($data['credit_transaction_ids']);
        if ($sum > 0) {
            $creditTransaction = ($this->storeCreditTransactionAction)($data['profile_id'], [
                'amount' => abs($sum),
                'description' => $data['credit_transaction_description'],
            ]);

            $data = array_merge($data, [
                'credit_transaction_id' => $creditTransaction->getKey(),
                'status' => Invoice::STATUS_PAID,
            ]);

            $invoice = ($this->storeInvoiceAction)($data);
            $invoice->credit_transaction_id = $creditTransaction->getKey();

            return $invoice;
        }

        return null;
    }
}
