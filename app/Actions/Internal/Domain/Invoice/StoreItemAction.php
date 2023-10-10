<?php

namespace App\Actions\Internal\Domain\Invoice;

use App\Actions\Admin\Invoice\Item\StoreItemAction as AdminStoreItemAction;
use App\Models\Invoice;

class StoreItemAction
{
    public function __construct(private readonly AdminStoreItemAction $storeItemAction)
    {
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        return ($this->storeItemAction)($invoice, $data);
    }
}
