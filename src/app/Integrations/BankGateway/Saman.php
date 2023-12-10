<?php

namespace App\Integrations\BankGateway;

use App\Exceptions\Http\BadRequestException;
use App\Integrations\BankGateway\Interface\BankGatewayInterface;
use App\Models\BankGateway;
use App\Models\Transaction;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Saman implements BankGatewayInterface
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
                'action' => 'token',
                'TerminalId' => $this->bankGateway->config['terminal_id'],
                'Amount' => $transaction->amount,
                'ResNum' => $transaction->getKey(),
                'RedirectUrl' => $callbackUrl
            ]);

        if ($response->json('status') != 1) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException('Saman  failed at start, status: ' . $response->json('status'));
        }

        ($this->updateTransactionService)($transaction, ['tracking_code' => $response->json('token'),]);

        return Str::finish($this->bankGateway->config['start_url'], '?token=') . $response->json('token');
    }

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction
    {
        if ($data['state'] != 'OK') {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }
        if ($data['ResNum'] != $transaction->getKey()) {
            throw new BadRequestException("Saman miss match transactionId, transactionId: $transaction->id , ResNum: " . $data['ResNum']);
        }

        $response = Http::withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->post($this->bankGateway->config['verify_url'], [
                'RefNum' => $transaction->tracking_code,
                'TerminalNumber' => $this->bankGateway->config['terminal_id'],
            ]);

        if (!$response->json('Success') || $response->json('ResultCode') != 0) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException('Saman verify ResultCode: ' . $response->json('ResultCode'));
        }

        return ($this->updateTransactionService)($transaction, [
            'status' => Transaction::STATUS_SUCCESS,
            'reference_id' => $response->json('TransactionDetail.RefNum'),
        ]);
    }
}
