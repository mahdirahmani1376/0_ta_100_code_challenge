<?php

namespace App\Services\Profile\Invoice;

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
