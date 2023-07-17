<?php

namespace App\Services\Admin\Transaction;

use App\Models\Invoice;
use App\Models\Wallet;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;

class StoreRefundCreditTransactionService
{
    private CreditTransactionRepositoryInterface $creditTransactionRepository;

    public function __construct(CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
        $this->creditTransactionRepository = $creditTransactionRepository;
    }

    public function __invoke(Invoice $invoice, Wallet $wallet)
    {
        $data = [
            'created_at' => $invoice->created_at,
            'amount' => $invoice->total,
            'invoice_id' => $invoice->getKey(),
            'client_id' => $invoice->client_id,
            'description' => __('finance.credit.RefundRefundedInvoiceCredit', [
                'invoice_id' => $invoice->getKey()
            ]),
            'wallet_id' => $wallet->getKey(),
        ];

        return $this->creditTransactionRepository->create($data, array_keys($data));
    }
}
