<?php

namespace App\Services\Admin\Wallet;

use App\Models\CreditTransaction;
use App\Models\Invoice;
use App\Models\Wallet;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;

class StoreCreditTransactionForOfflineTransactionService
{
    private OfflineTransactionRepositoryInterface $offlineTransactionRepository;
    private CreditTransactionRepositoryInterface $creditTransactionRepository;

    public function __construct(
        OfflineTransactionRepositoryInterface $offlineTransactionRepository,
        CreditTransactionRepositoryInterface  $creditTransactionRepository,
    )
    {
        $this->offlineTransactionRepository = $offlineTransactionRepository;
        $this->creditTransactionRepository = $creditTransactionRepository;
    }

    public function __invoke(Invoice $invoice, Wallet $wallet): ?CreditTransaction
    {
        $sumOfVerifiedOfflineTransactions = $this->offlineTransactionRepository->sumOfVerifiedOfflineTransactions($invoice);

        if ($sumOfVerifiedOfflineTransactions <= 0) {
            return null;
        }

        return $this->creditTransactionRepository->create([
            'client_id' => $invoice->client_id,
            'wallet_id' => $wallet->getKey(),
            'admin_id' => request('admin_id'),
            'amount' => $sumOfVerifiedOfflineTransactions,
            'description' => __('finance.credit.AddCreditInvoice', ['invoice_id' => $invoice->getKey()]),
        ], ['client_id', 'wallet_id', 'admin_id', 'amount', 'description',]);
    }
}
