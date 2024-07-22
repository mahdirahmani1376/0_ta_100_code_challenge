<?php

namespace App\Actions\Invoice\Item;

use App\Services\Invoice\Item\IndexItemInvoiceableTypeService;

class IndexItemInvoiceableTypesAction
{
    public function __construct(
        private readonly IndexItemInvoiceableTypeService $indexItemInvoiceableTypeService,
    )
    {
    }

    public function __invoke()
    {
        return ($this->indexItemInvoiceableTypeService)();
    }
}
