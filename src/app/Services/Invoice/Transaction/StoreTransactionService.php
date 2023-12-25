<?php

namespace App\Services\Invoice\Transaction;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class StoreTransactionService
{
    public function __construct(private readonly TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function __invoke(Invoice $invoice, array $data): Transaction
    {
        $data['invoice_id'] = $invoice->getKey();
        $data['profile_id'] = $invoice->profile_id;
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
            'profile_id',
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
