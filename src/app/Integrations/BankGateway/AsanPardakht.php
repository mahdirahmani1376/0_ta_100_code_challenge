<?php

namespace App\Integrations\BankGateway;

use App\Exceptions\Http\BadRequestException;
use App\Models\BankGateway;
use App\Models\Transaction;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AsanPardakht implements Interface\BankGatewayInterface
{
    private UpdateTransactionService $updateTransactionService;

    public function __construct(private readonly BankGateway $bankGateway)
    {
        $this->updateTransactionService = app(UpdateTransactionService::class);
    }

    public static function make(BankGateway $bankGateway): Interface\BankGatewayInterface
    {
        return new static($bankGateway);
    }

    public function getRedirectUrlToGateway(Transaction $transaction, string $callbackUrl): string
    {
        $response = Http::withHeader('Content-Type', 'application/json')
            ->withHeader('usr', $this->bankGateway->config['username'])
            ->withHeader('pwd', $this->bankGateway->config['password'])
            ->post($this->bankGateway->config['request_url'], [
                'serviceTypeId' => 1,
                'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
                'localInvoiceId' => $transaction->getKey(),
                'amountInRials' => $transaction->amount,
                'localDate' => now()->format("Ymd His"),
                'callbackURL' => $callbackUrl,
                'paymentId' => "0",
                'additionalData' => '',
            ]);

        if ($response->status() != Response::HTTP_OK) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException('AsanPardakht failed at start: ' . $response->status());
        }

        $trackingCode = Str::replace('"', '', $response->body());
        ($this->updateTransactionService)($transaction, ['tracking_code' => $trackingCode,]);

        return Str::finish($this->bankGateway->config['start_url'], '?RefId=') . $trackingCode;
    }

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction
    {
        $transactionResultResponse = Http::withHeader('Content-Type', 'application/json')
            ->withHeader('usr', $this->bankGateway->config['username'])
            ->withHeader('pwd', $this->bankGateway->config['password'])
            ->get($this->bankGateway->config['transaction_result_url'], [
                'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
                'localInvoiceId' => $transaction->getKey(),
            ]);

        if ($transactionResultResponse->status() != Response::HTTP_OK) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        $verifyResponse = Http::withHeader('Content-Type', 'application/json')
            ->withHeader('usr', $this->bankGateway->config['username'])
            ->withHeader('pwd', $this->bankGateway->config['password'])
            ->post($this->bankGateway->config['verify_url'], [
                'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
                'payGateTranId' => $transactionResultResponse->json('payGateTranID'),
            ]);

        if ($verifyResponse->status() != Response::HTTP_OK) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException('AsanPardakht verify status: ' . $verifyResponse->status());
        }

        $settlementResponse = Http::withHeader('Content-Type', 'application/json')
            ->withHeader('usr', $this->bankGateway->config['username'])
            ->withHeader('pwd', $this->bankGateway->config['password'])
            ->post($this->bankGateway->config['settlement_url'], [
                'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
                'payGateTranId' => $transactionResultResponse->json('payGateTranID'),
            ]);

        if ($settlementResponse->status() != Response::HTTP_OK) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException('AsanPardakht settlement status: ' . $settlementResponse->status());
        }

        return ($this->updateTransactionService)($transaction, [
            'status' => Transaction::STATUS_SUCCESS,
            'reference_id' => $transactionResultResponse->json('payGateTranID'),
        ]);
    }
}
