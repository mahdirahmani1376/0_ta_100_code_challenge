<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Services\Admin\OfflineTransaction\UpdateOfflineTransactionService;
use App\Services\Admin\Transaction\UpdateTransactionService;
use App\Services\Transaction\FindTransactionByTrackingCodeService;
use Illuminate\Support\Facades\DB;

class UpdateOfflineTransactionAction
{
    private UpdateOfflineTransactionService $updateOfflineTransactionService;
    private UpdateTransactionService $updateTransactionService;
    private FindTransactionByTrackingCodeService $findTransactionByTrackingCodeService;

    public function __construct(
        UpdateOfflineTransactionService      $updateOfflineTransactionService,
        UpdateTransactionService             $updateTransactionService,
        FindTransactionByTrackingCodeService $findTransactionByTrackingCodeService
    )
    {
        $this->updateOfflineTransactionService = $updateOfflineTransactionService;
        $this->updateTransactionService = $updateTransactionService;
        $this->findTransactionByTrackingCodeService = $findTransactionByTrackingCodeService;
    }

    public function __invoke(OfflineTransaction $offlineTransaction, array $data)
    {
        check_rahkaran($offlineTransaction->invoice);

        try {
            DB::beginTransaction();
            $transaction = ($this->findTransactionByTrackingCodeService)($offlineTransaction->tracking_code);

            $offlineTransaction = ($this->updateOfflineTransactionService)($offlineTransaction, $data);

            $transactionData = [
                'created_at' => $data['paid_at'],
                'tracking_code' => $data['tracking_code']
            ];
            ($this->updateTransactionService)($transaction, $transactionData);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $offlineTransaction;
    }
}
