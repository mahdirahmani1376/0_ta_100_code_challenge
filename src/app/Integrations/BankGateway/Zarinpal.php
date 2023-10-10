<?php

namespace App\Integrations\BankGateway;

use App\Exceptions\Http\BadRequestException;
use App\Integrations\BankGateway\Interface\BankGatewayInterface;
use App\Models\BankGateway;
use App\Models\Transaction;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Zarinpal implements BankGatewayInterface
{
    private UpdateTransactionService $updateTransactionService;

    public function __construct(private readonly BankGateway $bankGateway)
    {
        $this->updateTransactionService = app(UpdateTransactionService::class);
    }

    public static function make(BankGateway $bankGateway): BankGatewayInterface
    {
        return new static($bankGateway);
    }

    public function getRedirectUrlToGateway(Transaction $transaction, string $callbackUrl): string
    {
        $response = Http::withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->post($this->bankGateway->config['request_url'], [
                'merchant_id' => $this->bankGateway->config['merchant_id'],
                'amount' => $transaction->amount,
                'callback_url' => $callbackUrl,
                'description' => 'description', // TODO change this if needed
            ]);

        if ($response->json('data.code') != 100 || $response->json('data.code') != 101) {
            throw new BadRequestException('Zarinpal request code: ' . $response->json('data.code')); // TODO maybe use a custom exception class
        }

        ($this->updateTransactionService)($transaction, ['tracking_code' => $response->json('data.authority'),]);

        return Str::finish($this->bankGateway->config['start_url'], '/') . $response->json('data.authority');
    }

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction
    {
        if ($data['status'] == 'NOK') {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException('Zarinpal was not successful');
        }
        if ($data['Authority'] != $transaction->tracking_code) {
            throw new BadRequestException("Zarinpal miss match tracking_code, transactionId: $transaction->id , Authority: " . $data['Authority']);
        }

        $response = Http::withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->post($this->bankGateway->config['verify_url'], [
                'merchant_id' => $this->bankGateway->config['merchant_id'],
                'amount' => $transaction->amount,
                'authority' => $transaction->tracking_code,
            ]);

        if ($response->json('data.code') != 100 || $response->json('data.code') != 101) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException('Zarinpal verify code: ' . $response->json('data.code')); // TODO maybe use a custom exception class
        }

        return ($this->updateTransactionService)($transaction, [
            'status' => Transaction::STATUS_SUCCESS,
            'reference_id' => $response->json('data.ref_id'),
        ]);
    }
}
