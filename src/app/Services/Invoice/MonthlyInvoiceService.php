<?php

namespace App\Services\Invoice;

use App\Actions\Invoice\StoreInvoiceAction;
use App\Actions\Invoice\Transaction\StoreTransactionAction;
use App\Actions\Wallet\CreditTransaction\BulkDeleteCreditTransactionAction;
use App\Actions\Wallet\CreditTransaction\DeductBalanceAction;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Transaction;
use App\Services\Profile\FindOrCreateProfileService;
use App\Services\Tax\GetTaxExcludeService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlyInvoiceService
{
    public function __construct(
        private readonly FindOrCreateProfileService        $findOrCreateProfileService,
        private readonly GetTaxExcludeService              $getTaxExcludeService,
        private readonly StoreInvoiceAction                $storeInvoiceAction,
        private readonly BulkDeleteCreditTransactionAction $bulkDeleteCreditTransactionAction,
        private readonly DeductBalanceAction               $deductBalanceAction,
        private readonly StoreTransactionAction            $storeTransactionAction,
    )
    {
    }

    public function __invoke(array $data)
    {
        $invoice = null;
        $creditTransaction = null;


        try {
            DB::beginTransaction();

            $financeProfileId = ($this->findOrCreateProfileService)($data['client_id'])->id;

            $invoice = $this->createInvoice($data, $financeProfileId);

            if (!empty($invoice)) {
                $bulkDeleteResponse = ($this->bulkDeleteCreditTransactionAction)([
                    'credit_transaction_ids' => $data['credit_transaction_ids'],
                ]);

                if ($bulkDeleteResponse['sum'] != 0) {
                    $creditTransaction = ($this->deductBalanceAction)($financeProfileId, [
                        'amount'      => $bulkDeleteResponse['sum'],
                        'description' => __('finance.credit.ApplyCreditToInvoiceWithCloud', [
                            'invoice_id' => $invoice->id
                        ]),
                        'invoice_id'  => $invoice->id,
                    ]);

//                $invoice = $this->financeAPIService()->parseResponse()
//                    ->showInvoice($invoice->id, false);

                    ($this->storeTransactionAction)([
                        'invoice_id'     => $invoice->id,
                        'amount'         => $invoice->balance,
                        'created_at'     => data_get($data, 'invoice_paid_date') ?? now(),
                        'payment_method' => Transaction::PAYMENT_METHOD_CREDIT,
                        'tracking_code'  => 'NO_TRACKING_CODE'
                    ]);
                }
            }

            Log::info('check-monthly-command-log', [
                'id'                    => $invoice?->id,
                'credit_transaction_id' => $creditTransaction?->id,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info('check-monthly-command-log-error', [
                'error' => $e->getMessage()
            ]);
        }

        return [
            'id'                    => $invoice?->id,
            'credit_transaction_id' => $creditTransaction?->id,
        ];

    }

    private function createInvoice($data, $financeProfileId)
    {
        $items = [];
        foreach ($data['invoice_items'] as $item) {

            $amount = data_get(($this->getTaxExcludeService)(data_get($item, 'amount')), 'amount');

            $items[] = [
                'description'      => data_get($item, 'description'),
                'amount'           => $amount,
                'invoiceable_type' => Item::TYPE_CLOUD,
                'invoiceable_id'   => $item['rel_id'],
            ];
        }

        return ($this->storeInvoiceAction)([
            'profile_id'     => $financeProfileId,
            'items'          => $items,
            'payment_method' => Invoice::PAYMENT_METHOD_CREDIT,
            'invoice_date'   => data_get($data, 'invoice_created_at') ?? now()->format('Y-m-d H:i:s'),
            'due_date'       => data_get($data, 'invoice_due_date') ?? now()->addMonth()->format('Y-m-d H:i:s'),
            'paid_at'        => data_get($data, 'invoice_paid_date') ?? now()->format('Y-m-d H:i:s'),
            'status'         => Invoice::STATUS_UNPAID,
            'notification'   => false
        ]);
    }
}
