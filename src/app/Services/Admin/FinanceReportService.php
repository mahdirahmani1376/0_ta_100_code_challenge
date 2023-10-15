<?php

namespace App\Services\Admin;

use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use App\Repositories\Wallet\Interface\WalletRepositoryInterface;

class FinanceReportService
{
    public function __construct(
        private readonly OfflineTransactionRepositoryInterface $offlineTransactionRepository,
        private readonly TransactionRepositoryInterface        $transactionRepository,
        private readonly InvoiceRepositoryInterface            $invoiceRepository,
        private readonly WalletRepositoryInterface             $walletRepository,
        private readonly CreditTransactionRepositoryInterface  $creditTransactionRepository,
    )
    {
    }

    public function __invoke(int $view = 0)
    {
        // TODO implement cache in some form redis or just a mysql table with json fields ?
        return match ($view) {
            1 => [
                'revenue' => $this->invoiceRepository->reportRevenue(),
                'collection' => $this->invoiceRepository->reportCollection(),
                'tax' => $this->invoiceRepository->reportTax(),
                'wallet' => $this->walletRepository->reportSum(),
                'credit_transaction' => $this->creditTransactionRepository->report(),
                'rahkaran' => [
                    'invoice' => $this->invoiceRepository->rahkaranQuery()->count(),
                    'transaction' => $this->transactionRepository->rahkaranQuery()->count(),
                ],
                'gateway' => [
                    'transaction' => $this->transactionRepository->reportRevenueBasedOnGateway(),
                ],
            ],
            default => [
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
            ],
        };
    }
}
