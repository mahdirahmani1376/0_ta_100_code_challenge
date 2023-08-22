<?php

namespace App\Actions\Admin\Invoice;

use App\Actions\Admin\Wallet\ShowWalletAction;
use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Services\Admin\Invoice\Item\StoreItemService;
use App\Services\Admin\Invoice\StoreInvoiceService;
use App\Services\Admin\Transaction\StoreRefundCreditTransactionService;
use App\Services\Admin\Transaction\StoreRefundTransactionService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\ProcessInvoiceService;

class StoreInvoiceAction
{
    private StoreInvoiceService $storeInvoiceService;
    private StoreItemService $storeItemService;
    private StoreRefundCreditTransactionService $storeRefundCreditTransactionService;
    private StoreRefundTransactionService $storeRefundTransactionService;
    private ShowWalletAction $showWalletAction;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;
    private ProcessInvoiceService $processInvoiceService;
    private ProcessInvoiceAction $processInvoiceAction;

    public function __construct(StoreInvoiceService                 $storeInvoiceService,
                                StoreItemService                    $storeItemService,
                                StoreRefundCreditTransactionService $storeRefundCreditTransactionService,
                                StoreRefundTransactionService       $storeRefundTransactionService,
                                ProcessInvoiceService               $processInvoiceService,
                                ShowWalletAction                    $showWalletAction,
                                CalcInvoicePriceFieldsService       $calcInvoicePriceFieldsService,
                                ProcessInvoiceAction                $processInvoiceAction
    )
    {
        $this->storeInvoiceService = $storeInvoiceService;
        $this->storeItemService = $storeItemService;
        $this->storeRefundCreditTransactionService = $storeRefundCreditTransactionService;
        $this->storeRefundTransactionService = $storeRefundTransactionService;
        $this->showWalletAction = $showWalletAction;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
        $this->processInvoiceService = $processInvoiceService;
        $this->processInvoiceAction = $processInvoiceAction;
    }

    public function __invoke(array $data)
    {
        $invoice = ($this->storeInvoiceService)($data);

        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                ($this->storeItemService)($invoice, $item);
            }
        }
        // Calculate sub_total, tax, total fields of invoice
        $invoice = ($this->calcInvoicePriceFieldsService)($invoice);
        // TODO check if invoice is paid refunded or ...
//        ($this->processInvoiceAction)($invoice);

        return $invoice;
    }
}
