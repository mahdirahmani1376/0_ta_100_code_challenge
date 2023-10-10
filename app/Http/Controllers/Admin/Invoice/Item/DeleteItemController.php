<?php

namespace App\Http\Controllers\Admin\Invoice\Item;

use App\Actions\Admin\Invoice\Item\DeleteItemAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Resources\Admin\Invoice\InvoiceResource;
use App\Models\Invoice;
use App\Models\Item;

class DeleteItemController
{
    public function __construct(private readonly DeleteItemAction $deleteItemAction)
    {
    }

    /**
     * @param Invoice $invoice
     * @param Item $item
     * @return InvoiceResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(Invoice $invoice, Item $item)
    {
        $invoice = ($this->deleteItemAction)($invoice, $item);

        return InvoiceResource::make($invoice);
    }
}
