<?php

namespace App\Integrations\BankGateway\Interface;

use App\Models\BankGateway;
use App\Models\Transaction;
use Illuminate\Http\Request;

interface BankGatewayInterface
{
    public static function make(BankGateway $bankGateway): self;

    public function getRedirectUrlToGateway(Transaction $transaction, string $callbackUrl): string;

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction;
}
