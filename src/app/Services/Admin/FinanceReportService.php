<?php

namespace App\Services\Admin;

use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class FinanceReportService
{
    public function __construct(
        private readonly OfflineTransactionRepositoryInterface $offlineTransactionRepository,
        private readonly TransactionRepositoryInterface        $transactionRepository,
        private readonly InvoiceRepositoryInterface            $invoiceRepository,
    )
    {
    }

    public function __invoke()
    {
        return [
            'offline_transaction_today_count' => $this->offlineTransactionRepository->countToday(),
            'offline_transaction_rejected_count' => $this->offlineTransactionRepository->countRejected(),
            'offline_transaction_latest' => $this->offlineTransactionRepository->reportLatest(),
            'transaction_count' => $this->transactionRepository->count(),
            'transaction_today_approved_count' => $this->transactionRepository->successCount(),
            'transaction_today_rejected_count' => $this->transactionRepository->failCount(),
            'transactions_latest' => $this->transactionRepository->reportLatest(),
            'invoice_count' => $this->invoiceRepository->count(),
            'invoice_today_count' => $this->invoiceRepository->countToday(),
            'invoice_paid_count' => $this->invoiceRepository->countPaid(),
            'invoice_income_today' => $this->invoiceRepository->incomeToday(),
            'invoice_latest' => $this->invoiceRepository->reportLatest(),
        ];
    }
}
