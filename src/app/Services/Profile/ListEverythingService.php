<?php

namespace App\Services\Profile;

use App\Models\CreditTransaction;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;

class ListEverythingService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface           $invoiceRepository,
        private readonly TransactionRepositoryInterface       $transactionRepository,
        private readonly CreditTransactionRepositoryInterface $creditTransactionRepository,
    )
    {
    }

    public function __invoke(int $clientId, int $offset = null, $perPage = 100)
    {
        $invoices = $this->invoiceRepository->profileListEverything($clientId)
            ->map(function (Invoice $invoice) {
                return [
                    'id' => $invoice->getKey(),
                    'created_at' => $invoice->paid_at?->toDateTimeString(),
                    'invoice_id' => $invoice->getKey(),
                    'amount' => $invoice->total,
                    'type' => 'invoice',
                    'description' => null,
                ];
            });
        $transactions = $this->transactionRepository->profileListEverything($clientId)
            ->map(function (Transaction $transaction) {
                return [
                    'id' => $transaction->getKey(),
                    'created_at' => $transaction->updated_at?->toDateTimeString(),
                    'invoice_id' => $transaction->invoice_id,
                    'amount' => $transaction->amount,
                    'type' => 'transaction',
                    'description' => $transaction->description,
                ];
            });
        $creditTransaction = $this->creditTransactionRepository->profileListEverything($clientId)
            ->map(function (CreditTransaction $creditTransaction) {
                return [
                    'id' => $creditTransaction->getKey(),
                    'created_at' => $creditTransaction->created_at?->toDateTimeString() ?? $creditTransaction->updated_at?->toDateTimeString(),
                    'invoice_id' => $creditTransaction->invoice_id,
                    'amount' => $creditTransaction->amount,
                    'type' => 'credit_transaction',
                    'description' => $creditTransaction->description,
                ];
            });

        return collect([...$invoices, ...$transactions, ...$creditTransaction])
            ->sortByDesc(fn($item) => $item['created_at'])
            ->slice($offset ?? 0 * $perPage, $perPage)
            ->values()
            ->toArray();
    }
}
