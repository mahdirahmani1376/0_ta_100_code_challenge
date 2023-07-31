<?php

namespace App\Actions\Profile\Invoice;

use App\Http\Resources\Profile\Invoice\StoreMassPaymentInvoiceService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class StoreMassPaymentInvoiceAction
{
    private StoreMassPaymentInvoiceService $storeMassPaymentInvoiceService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(
        StoreMassPaymentInvoiceService $storeMassPaymentInvoiceService,
        CalcInvoicePriceFieldsService  $calcInvoicePriceFieldsService
    )
    {
        $this->storeMassPaymentInvoiceService = $storeMassPaymentInvoiceService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
    }

    public function __invoke(array $data)
    {
        $massPaymentInvoice = ($this->storeMassPaymentInvoiceService)($data);

        return ($this->calcInvoicePriceFieldsService)($massPaymentInvoice);
    }
}
