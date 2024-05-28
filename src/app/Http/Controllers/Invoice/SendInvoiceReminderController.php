<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\SendInvoiceReminderAction;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Response;

class SendInvoiceReminderController extends Controller
{
    public function __construct(private readonly SendInvoiceReminderAction $sendInvoiceReminderAction)
    {
        parent::__construct();
    }

    public function __invoke(Invoice $invoice)
    {
        ($this->sendInvoiceReminderAction)($invoice);

        return response()->json(status: Response::HTTP_ACCEPTED);
    }
}