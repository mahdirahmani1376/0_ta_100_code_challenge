<?php

namespace App\Integrations\BankGateway;

use App\Models\Invoice;
use Illuminate\Support\Str;

class BaseBankGateway
{
    protected function getFailedRedirectUrl(Invoice $invoice, $callbackUrl): string
    {
        $isCloud = Str::afterLast($callbackUrl, '/') == 'cloud';
        $rawRedirectUrl = $isCloud ?
            config('payment.bank_gateway.result_cloud_redirect_url') :
            config('payment.bank_gateway.result_redirect_url');

        return callback_result_redirect_url($rawRedirectUrl, $invoice->id, invoiceStatus: $invoice->status);
    }
}