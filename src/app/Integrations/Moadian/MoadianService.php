<?php

namespace App\Integrations\Moadian;

use App\Models\Invoice;
use App\Models\MoadianLog;
use Jooyeshgar\Moadian\Facades\Moadian;
use Jooyeshgar\Moadian\Invoice as MoadianInvoice;

class MoadianService
{
    public static function buildMoadianInvoice(Invoice $invoice): MoadianInvoice
    {
        return (new MoadianFactory)->createMoadianInvoiceDTO($invoice);
    }

    public static function sendInvoice(Invoice $invoice): void
    {
        $moadianInvoice = self::buildMoadianInvoice($invoice);

        /** @var MoadianLog $moadianLog */
        $moadianLog = MoadianLog::query()->create([
            'invoice_id' => $invoice->invoice_id,
            'tax_id'     => $moadianInvoice->toArray()['header']['taxid'],
            'status'     => MoadianLog::STATUS_INIT,
        ]);

        $response = Moadian::sendInvoice($moadianInvoice);
        $body = $response->getBody();
        $moadianLog->status = MoadianLog::STATUS_PENDING;
        $moadianLog->reference_code = $body['result'][0]['referenceNumber'];
        $moadianLog->save();
    }

    public static function inquiryMoadian(MoadianLog $moadianLog): array
    {
        $response = Moadian::inquiryByReferenceNumbers($moadianLog->reference_code)->getBody()[0];
        $status = $response['status'];
        if ($status == 'PENDING') {
            return $response;
        }
        $moadianLog->status = match ($status) {
            'SUCCESS' => MoadianLog::STATUS_SUCCESS,
            'FAILED', 'NOT_FOUND' => MoadianLog::STATUS_FAILURE,
        };
        $moadianLog->error = match ($status) {
            'SUCCESS' => null,
            'FAILED', 'NOT_FOUND' => data_get($response, 'data.error', 'RECORD_NOT_FOUND'),
        };
        $moadianLog->save();

        return $response;
    }
}
