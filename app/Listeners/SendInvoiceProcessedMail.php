<?php

namespace App\Listeners;

use App\Events\InvoiceProcessed;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\Rahkaran\ValueObjects\Client;
use Illuminate\Support\Facades\Mail;

class SendInvoiceProcessedMail
{
    public function handle(InvoiceProcessed $event): void
    {
        /** @var Client $client */
        $client = MainAppAPIService::getClients($event->invoice->client_id)[0];

        if ($client->email) {
            Mail::to($client->email)->send(new \App\Mail\InvoiceProcessed($event->invoice));
        }
    }
}
