<?php

namespace App\Listeners;

use App\Events\InvoiceProcessed;
use App\Integrations\MainApp\MainAppAPIService;
use App\Models\AdminLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class SignalMainAppToProcessInvoice implements ShouldQueue
{
    public function handle(InvoiceProcessed $event): void
    {
        MainAppAPIService::signalMainAppToProcessInvoice($event->invoice);
    }
}
