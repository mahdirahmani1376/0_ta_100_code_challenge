<?php

namespace App\Actions\Profile\Invoice;

use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Profile\Invoice\StoreMassPaymentInvoiceService;

class StoreMassPaymentInvoiceAction
{
    public function __construct(
        private readonly StoreMassPaymentInvoiceService $storeMassPaymentInvoiceService,
        private readonly CalcInvoicePriceFieldsService  $calcInvoicePriceFieldsService
    )
    {
    }

    public function __invoke(array $data)
    {
        $massPaymentInvoice = ($this->storeMassPaymentInvoiceService)($data);

        return ($this->calcInvoicePriceFieldsService)($massPaymentInvoice);
    }
}
