<?php

namespace App\Services\Admin\OfflineTransaction;

use App\Models\Invoice;
use App\Models\OfflineTransaction;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;

class StoreOfflineTransactionService
{
    public function __construct(private readonly OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
    }

    public function __invoke(Invoice $invoice, array $data): OfflineTransaction
    {
        $data['invoice_id'] = $invoice->getKey();
        $data['status'] = OfflineTransaction::STATUS_PENDING;
        $data['client_id'] = $invoice->client_id;

        return $this->offlineTransactionRepository->create($data, [
            'paid_at',
            'client_id',
            'invoice_id',
            'bank_account_id',
            'admin_id',
            'status',
            'payment_method',
            'tracking_code',
            'mobile',
            'description',
            'amount',
        ]);
    }
}
