<?php

namespace App\Http\Controllers\Public\BankGateway;

use App\Actions\Public\BankGateway\CallbackFromGatewayAction;
use App\Models\Transaction;

class CallbackFromGatewayController
{
    public function __construct(private readonly CallbackFromGatewayAction $callbackFromGatewayAction)
    {
    }

    public function __invoke(string $gateway, Transaction $transaction, string $source = null)
    {
        $redirectUrl = ($this->callbackFromGatewayAction)($transaction, $gateway, $source, request()->all());

        return response()->json(['redirect_url' => $redirectUrl,]);
    }
}
