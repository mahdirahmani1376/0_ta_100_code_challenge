<?php

namespace App\Http\Controllers\BankGateway;

use App\Actions\BankGateway\CallbackFromGatewayAction;
use App\Models\BankGateway;
use App\Models\Transaction;

class CallbackFromGatewayController
{
    public function __construct(private readonly CallbackFromGatewayAction $callbackFromGatewayAction)
    {
    }

    public function __invoke(string $bankGateway, Transaction $transaction, string $source = null)
    {
        $redirectUrl = ($this->callbackFromGatewayAction)($transaction, $bankGateway, $source, request()->all());

        return response()->json(['redirect_url' => $redirectUrl,]);
    }
}
