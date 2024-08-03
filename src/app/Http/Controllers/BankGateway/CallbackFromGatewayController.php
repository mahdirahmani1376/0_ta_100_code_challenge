<?php

namespace App\Http\Controllers\BankGateway;

use App\Actions\BankGateway\CallbackFromGatewayAction;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CallbackFromGatewayController extends Controller
{
    public function __construct(private readonly CallbackFromGatewayAction $callbackFromGatewayAction)
    {
        parent::__construct();
    }

    public function __invoke(
        Request     $request,
        string      $bankGateway,
        Transaction $transaction,
        string      $source = null
    )
    {
        $redirectUrl = ($this->callbackFromGatewayAction)($transaction, $bankGateway, $source, $request->all());

        return response()->json(['redirect_url' => $redirectUrl,]);
    }
}
