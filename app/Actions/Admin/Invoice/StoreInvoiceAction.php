<?php

namespace App\Actions\Admin\Invoice;

use App\Actions\Admin\Wallet\ShowWalletAction;
use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Actions\Invoice\ProcessInvoiceAction;
use App\Models\Invoice;
use App\Services\Admin\Invoice\Item\StoreItemService;
use App\Services\Admin\Invoice\StoreInvoiceService;
use App\Services\Admin\Transaction\StoreRefundCreditTransactionService;
use App\Services\Admin\Transaction\StoreRefundTransactionService;
use App\Services\Wallet\FindWalletByClientIdService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreInvoiceAction
{
    private StoreInvoiceService $storeInvoiceService;
    private StoreItemService $storeItemService;
    private StoreRefundCreditTransactionService $storeRefundCreditTransactionService;
    private StoreRefundTransactionService $storeRefundTransactionService;
    private ProcessInvoiceAction $processInvoiceAction;
    private CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction;
    private ShowWalletAction $showWalletAction;

    public function __construct(StoreInvoiceService                 $storeInvoiceService,
                                StoreItemService                    $storeItemService,
                                StoreRefundCreditTransactionService $storeRefundCreditTransactionService,
                                StoreRefundTransactionService       $storeRefundTransactionService,
                                CalcInvoicePriceFieldsAction        $calcInvoicePriceFieldsAction,
                                ProcessInvoiceAction                $processInvoiceAction,
                                ShowWalletAction $showWalletAction
    )
    {
        $this->storeInvoiceService = $storeInvoiceService;
        $this->storeItemService = $storeItemService;
        $this->storeRefundCreditTransactionService = $storeRefundCreditTransactionService;
        $this->storeRefundTransactionService = $storeRefundTransactionService;
        $this->processInvoiceAction = $processInvoiceAction;
        $this->calcInvoicePriceFieldsAction = $calcInvoicePriceFieldsAction;
        $this->showWalletAction = $showWalletAction;
    }

    public function __invoke(array $data)
    {
        try {
            DB::beginTransaction();
            $invoice = ($this->storeInvoiceService)($data);

            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    ($this->storeItemService)($invoice, $item);
                }
            }
            // Calculate sun_total, tax, total fields of invoice
            $invoice = ($this->calcInvoicePriceFieldsAction)($invoice);
            $invoice = ($this->processInvoiceAction)($invoice);

            if ($data['status'] == Invoice::STATUS_REFUNDED) {
                $wallet = ($this->showWalletAction)($invoice->client_id);
                ($this->storeRefundCreditTransactionService)($invoice, $wallet);
                ($this->storeRefundTransactionService)($invoice);

                ($this->processInvoiceAction)($invoice);
            }

            DB::commit();

            return $invoice;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            throw $exception;
        }
    }
}
