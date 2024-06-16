<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\MergeInvoiceAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Exceptions\SystemException\MergeInvoiceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\MergeInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;

class MergeInvoiceController extends Controller
{
    public function __construct(private readonly MergeInvoiceAction $mergeInvoiceAction)
    {
        parent::__construct();
    }

    /**
     * @param MergeInvoiceRequest $request
     * @return InvoiceResource
     * @throws MergeInvoiceException
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(MergeInvoiceRequest $request)
    {
        $invoice = ($this->mergeInvoiceAction)($request->validated());

        return InvoiceResource::make($invoice);
    }
}
