<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class CalcInvoicePaidAtService
{
    private InvoiceRepositoryInterface $invoiceRepository;
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(InvoiceRepositoryInterface     $invoiceRepository,
                                TransactionRepositoryInterface $transactionRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(Invoice $invoice, string $status): Invoice
    {
        // Do not change paid_at
        if ($invoice->balance != 0) {
            return $invoice;
        }
        // Do not change paid_at
        if (!in_array($status, [
            Invoice::STATUS_PAID,
            Invoice::STATUS_REFUNDED,
        ])) {
            return $invoice;
        }

        // Try to find the last successful transaction and use its 'created_at' timestamp as when invoice was paid at
        $lastSuccessfulTransactionCreatedAt = $this->transactionRepository->getLastSuccessfulTransaction($invoice);
        if (!is_null($lastSuccessfulTransactionCreatedAt)) {
            return $this->invoiceRepository->update(
                $invoice,
                ['paid_at' => $lastSuccessfulTransactionCreatedAt,],
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
