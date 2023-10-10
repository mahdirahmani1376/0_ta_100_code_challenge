<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\ManualCheckAction;
use App\Http\Requests\Admin\Invoice\ManualCheckRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;
use App\Models\Invoice;

class ManualCheckController
{
    public function __construct(private readonly ManualCheckAction $manualCheckAction)
    {
    }

    /**
     * @param Invoice $invoice
     * @param ManualCheckRequest $request
     * @return InvoiceResource
     */
    public function __invoke(Invoice $invoice, ManualCheckRequest $request)
    {
        $invoice = ($this->manualCheckAction)($invoice, $request->validated('admin_id'));

        return InvoiceResource::make($invoice);
    }
}
