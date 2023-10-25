<?php

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Helpers\JalaliCalender;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\MainApp\MainAppConfig;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInvoiceCreateEmail implements ShouldQueue
{
    public function handle(InvoiceCreated $event): void
    {
        try {
            $subject = MainAppConfig::get(MainAppConfig::FINANCE_INVOICE_CREATE_SUBJECT);
            $messageTemplate = MainAppConfig::get(MainAppConfig::FINANCE_INVOICE_CREATE_MESSAGE);
            $client = MainAppAPIService::getClients($event->invoice->client_id)[0];
            $message = parse_string($messageTemplate, [
                'client_name' => $client->full_name,
                'invoice_id' => $event->invoice->id,
                'created_at' => JalaliCalender::carbonToJalali($event->invoice->created_at),
            ]);

            MainAppAPIService::sendInvoiceCreateEmail($event->invoice->client_id, $subject, $message);
        } catch (\Exception $exception) {
            // TODO log the error
        }
    }
}
