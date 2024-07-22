<?php

namespace App\Http\Controllers\Invoice\Item;

use App\Actions\Invoice\Item\IndexItemInvoiceableTypesAction;
use App\Http\Controllers\Controller;

class IndexItemInvoiceableTypesController extends Controller
{
    public function __construct(private readonly IndexItemInvoiceableTypesAction $indexItemInvoiceableTypesAction)
    {
        parent::__construct();
    }

    public function __invoke()
    {
        return [
            'invoiceableTypes' => ($this->indexItemInvoiceableTypesAction)()
        ];
    }
}
