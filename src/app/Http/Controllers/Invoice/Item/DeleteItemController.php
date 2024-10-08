<?php

namespace App\Http\Controllers\Invoice\Item;

use App\Actions\Invoice\Item\DeleteItemAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Models\Invoice;
use App\Models\Item;

class DeleteItemController extends Controller
{
    public function __construct(private readonly DeleteItemAction $deleteItemAction)
    {
        parent::__construct();
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
