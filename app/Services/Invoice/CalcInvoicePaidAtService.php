<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class CalcInvoicePaidAtService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface     $invoiceRepository,
        private readonly TransactionRepositoryInterface $transactionRepository,
    )
    {
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        // Do not change paid_at
        if ($invoice->balance != 0) {
            return $invoice;
        }
        // Do not change paid_at
        if (!in_array($invoice->status, [
            Invoice::STATUS_PAID,
            Invoice::STATUS_REFUNDED,
        ])) {
            return $invoice;
        }

        // Try to find the last successful transaction and use its 'created_at' timestamp as when invoice was paid at
        /** @var Transaction $lastSuccessfulTransaction */
        $lastSuccessfulTransaction = $this->transactionRepository->getLastSuccessfulTransaction($invoice);
        if (!is_null($lastSuccessfulTransaction)) {
            return $this->invoiceRepository->update(
                $invoice,
                ['paid_at' => $lastSuccessfulTransaction->created_at,],
                ['paid_at',]
            );
        }

        // If of the above happened use invoice's created_at as for when it was paid at !!
        return $this->invoiceRepository->update(
            $invoice,
            ['paid_at' => $invoice->created_at,],
            ['paid_at',]
        );
    }
}
