<?php

namespace App\Services\Admin\OfflineTransaction;

use App\Models\Invoice;
use App\Models\OfflineTransaction;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;

class AttachOfflineTransactionToNewInvoiceService
{
    private OfflineTransactionRepositoryInterface $offlineTransactionRepository;

    public function __construct(OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
        $this->offlineTransactionRepository = $offlineTransactionRepository;
    }

    public function __invoke(OfflineTransaction $offlineTransaction, Invoice $newInvoice)
    {
        return $this->offlineTransactionRepository->update($offlineTransaction, [
            'invoice_id' => $newInvoice->getKey(),
            'status' => OfflineTransaction::STATUS_CONFIRMED,
        ], ['invoice_id', 'status']);
    }
}
