<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\ManualCheckAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\ManualCheckRequest;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Integrations\MainApp\MainAppConfig;
use App\Models\Invoice;

class ManualCheckController extends Controller
{
    public function __construct(private readonly ManualCheckAction $manualCheckAction)
    {
        parent::__construct();
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
