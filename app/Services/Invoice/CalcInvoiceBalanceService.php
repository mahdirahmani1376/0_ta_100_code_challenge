<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class CalcInvoiceBalanceService
{
    private TransactionRepositoryInterface $transactionRepository;
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository,
                                InvoiceRepositoryInterface     $invoiceRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        $sumOfPaidTransactions = $this->transactionRepository->sumOfPaidTransactions($invoice);
        dump($sumOfPaidTransactions);
        $balance = $invoice->total - $sumOfPaidTransactions;

        return $this->invoiceRepository->update(
            $invoice,
            ['balance' => $balance,],
            ['balance'],
        );
    }
}
