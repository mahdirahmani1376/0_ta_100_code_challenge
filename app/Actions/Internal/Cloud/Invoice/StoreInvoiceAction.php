<?php

namespace App\Actions\Internal\Cloud\Invoice;

use App\Actions\Admin\Invoice\StoreInvoiceAction as AdminStoreInvoiceAction;
use App\Actions\Admin\Invoice\Transaction\StoreTransactionAction;
use App\Actions\Invoice\ProcessInvoiceAction;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\Wallet\FindCreditTransactionByIdService;
use App\Services\Wallet\UpdateCreditTransactionService;
use Illuminate\Support\Arr;

class StoreInvoiceAction
{
    public function __construct(
        private readonly ProcessInvoiceAction             $processInvoiceAction,
        private readonly UpdateCreditTransactionService   $updateCreditTransactionService,
        private readonly FindCreditTransactionByIdService $findCreditTransactionByIdService,
        private readonly StoreTransactionAction           $storeTransactionAction,
        private readonly AdminStoreInvoiceAction          $adminStoreInvoiceAction,
    )
    {
    }

    public function __invoke(array $data)
    {
        $invoice = ($this->adminStoreInvoiceAction)(Arr::add($data, 'admin_id', 1));
        if ($data['status'] == Invoice::STATUS_PAID) {
            if (!empty($data['credit_transaction_id'])) {
                $creditTransaction = ($this->findCreditTransactionByIdService)($data['credit_transaction_id']);
                ($this->updateCreditTransactionService)($creditTransaction, [
                    'invoice_id' => $invoice->getKey(),
                    'description' => __('finance.credit.ApplyCreditToInvoiceWithCloud', ['invoice_id' => $invoice->getKey()])
                ]);
            }

            ($this->storeTransactionAction)($invoice, [
                'created_at' => $data['paid_at'] ?? now(),
                'amount' => $invoice->balance,
                'payment_method' => Transaction::PAYMENT_METHOD_CREDIT,
                'status' => Transaction::STATUS_SUCCESS,
                'reference_id' => Transaction::PREFIX_CREDIT_TRANSACTION . data_get($data, 'credit_transaction_id'),
            ]);
        }

        if (in_array($data['status'], [Invoice::STATUS_PAID, Invoice::STATUS_COLLECTIONS])) {
            ($this->processInvoiceAction)($invoice);
        }

        return $invoice;
    }
}
