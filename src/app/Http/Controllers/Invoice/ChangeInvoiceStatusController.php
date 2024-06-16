<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\ChangeInvoiceStatusAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\ChangeInvoiceStatusRequest;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Models\Invoice;

class ChangeInvoiceStatusController extends Controller
{
    public function __construct(private readonly ChangeInvoiceStatusAction $changeInvoiceStatusAction)
    {
        parent::__construct();
    }

    /**
     * @param Invoice $invoice
     * @param ChangeInvoiceStatusRequest $request
     * @return InvoiceResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(Invoice $invoice, ChangeInvoiceStatusRequest $request)
    {
        $invoice = ($this->changeInvoiceStatusAction)($invoice, $request->validated('status'));

        return InvoiceResource::make($invoice);
    }
}
