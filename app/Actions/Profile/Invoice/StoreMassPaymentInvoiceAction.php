<?php

namespace App\Actions\Profile\Invoice;

use App\Http\Resources\Profile\Invoice\StoreMassPaymentInvoiceService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

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
