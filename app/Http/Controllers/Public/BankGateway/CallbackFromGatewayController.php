<?php

namespace App\Http\Controllers\Public\BankGateway;

use App\Actions\Public\BankGateway\CallbackFromGatewayAction;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CallbackFromGatewayController
{
    public function __construct(private readonly CallbackFromGatewayAction $callbackFromGatewayAction)
    {
    }

    public function __invoke(Transaction $transaction, string $gateway, ?string $source, Request $request)
    {
        return ($this->callbackFromGatewayAction)($transaction, $gateway, $source, $request->all());
    }
}
