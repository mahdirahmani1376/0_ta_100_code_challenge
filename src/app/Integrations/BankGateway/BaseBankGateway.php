<?php

namespace App\Integrations\BankGateway;

use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BaseBankGateway
{
    protected function getFailedRedirectUrl(Transaction $transaction, $callbackUrl): string
    {
        $isCloud = Str::afterLast($callbackUrl, '/') == 'cloud';
        $rawRedirectUrl = $isCloud ?
            config('payment.bank_gateway.result_cloud_redirect_url') :
            config('payment.bank_gateway.result_redirect_url');

        return callback_result_redirect_url(
            url: $rawRedirectUrl,
            invoiceId: $transaction->invoice->id,
            transactionStatus: $transaction->status
        );
    }

    protected function callbackLog(Transaction $transaction, $data): void
    {
        Log::info(__CLASS__ . ' callback from gateway result.', [
            'transaction' => $transaction->toArray(),
            'data'        => $data
        ]);
    }
}