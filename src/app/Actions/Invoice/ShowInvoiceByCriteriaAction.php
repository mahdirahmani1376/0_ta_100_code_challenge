<?php

namespace App\Actions\Invoice;

use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\ShowInvoiceByCriteriaService;

class ShowInvoiceByCriteriaAction
{
    public function __construct(
        private readonly ShowInvoiceByCriteriaService  $showInvoiceByCriteriaService,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService,
    )
    {

    }

    public function __invoke(array $criteria = [], $recalculate = true)
    {
        $invoice = ($this->showInvoiceByCriteriaService)($criteria, true);
        
        return $recalculate ? ($this->calcInvoicePriceFieldsService)($invoice) : $invoice;
    }
}
