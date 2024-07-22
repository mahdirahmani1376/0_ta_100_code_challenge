<?php

namespace App\Services\Invoice\Item;

use App\Models\Item;

class IndexItemInvoiceableTypeService
{
    public function __invoke(): array
    {
        return Item::Invoiceable_Types;
    }
}
