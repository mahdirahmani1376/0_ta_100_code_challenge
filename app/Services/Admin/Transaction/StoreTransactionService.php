<?php

namespace App\Services\Admin\Transaction;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class StoreTransactionService
{
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(Invoice $invoice, array $data): Transaction
    {
        $data['invoice_id'] = $invoice->getKey();
        $data['client_id'] = $invoice->client_id;
        $data['status'] = $data['status'] ?? Transaction::STATUS_SUCCESS;
        if (empty($data['created_at'])) {
            $data['created_at'] = now();
        }
        if (empty($data['description'])) {
            $data['description'] = ' ';
        }
        $data['description'] .= '---Record created by Admin #' . request('admin_id', -1);
        if (empty($data['tracking_code'])) {
            $data['tracking_code'] = 'NO_TRACKING_CODE';
        }
        if (empty($data['reference_id'])) {
            $data['reference_id'] = 'NO_REFERENCE_' . $data['tracking_code'];
        }
        $data['ip'] = Request::createFromGlobals()->header('x-forwarded-for');

        return $this->transactionRepository->create($data, [
            'invoice_id',
            'client_id',
            'status',
            'created_at',
            'amount',
            'reference_id',
            'description',
            'payment_method',
            'tracking_code',
            'ip',
        ]);
    }
}
