<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\ManualCheckAction;
use App\Http\Requests\Admin\Invoice\ManualCheckRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;
use App\Models\Invoice;

class ManualCheckController
{
    private ManualCheckAction $manualCheckAction;

    public function __construct(ManualCheckAction $manualCheckAction)
    {
        $this->manualCheckAction = $manualCheckAction;
    }

    public function __invoke(Invoice $invoice, ManualCheckRequest $request)
    {
        $invoice = ($this->manualCheckAction)($invoice, $request->validated('admin_id'));

        return InvoiceResource::make($invoice);
    }
}
