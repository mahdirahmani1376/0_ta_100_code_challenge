<?php

namespace App\Integrations\BankGateway;

use App\Exceptions\Http\BadRequestException;
use App\Models\BankGateway;
use App\Models\Transaction;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AsanPardakht extends BaseBankGateway implements Interface\BankGatewayInterface
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
                'serviceTypeId'           => 1,
                'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
                'localInvoiceId'          => $transaction->getKey(),
                'amountInRials'           => $transaction->amount,
                'localDate'               => now()->format("Ymd His"),
                'callbackURL'             => $callbackUrl,
                'paymentId'               => "0",
                'additionalData'          => '',
            ]);

        if ($response->status() != Response::HTTP_OK) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            return $this->getFailedRedirectUrl($transaction, $transaction->callback_url);
        }

        $trackingCode = Str::replace('"', '', $response->body());
        ($this->updateTransactionService)($transaction, ['tracking_code' => $trackingCode,]);

        return Str::finish($this->bankGateway->config['start_url'], '?RefId=') . $trackingCode;
    }

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction
    {
        $this->callbackLog($transaction, $data);

        $transactionResultResponse = Http::withHeader('Content-Type', 'application/json')
            ->withHeader('usr', $this->bankGateway->config['username'])
            ->withHeader('pwd', $this->bankGateway->config['password'])
            ->get($this->bankGateway->config['transaction_result_url'], [
                'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
                'localInvoiceId'          => $transaction->getKey(),
            ]);

        if ($transactionResultResponse->status() != Response::HTTP_OK) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        $amount = data_get($data, 'Amount');
        $transactionId = data_get($data, 'PayGateTranID');
        if ($amount != $transaction->amount || $transactionId != $transaction->getKey()) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        $verifyResponse = Http::withHeader('Content-Type', 'application/json')
            ->withHeader('usr', $this->bankGateway->config['username'])
            ->withHeader('pwd', $this->bankGateway->config['password'])
            ->post($this->bankGateway->config['verify_url'], [
                'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
                'payGateTranId'           => $transactionResultResponse->json('payGateTranID'),
            ]);

        if ($verifyResponse->status() != Response::HTTP_OK) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        $settlementResponse = Http::withHeader('Content-Type', 'application/json')
            ->withHeader('usr', $this->bankGateway->config['username'])
            ->withHeader('pwd', $this->bankGateway->config['password'])
            ->post($this->bankGateway->config['settlement_url'], [
                'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
                'payGateTranId'           => $transactionResultResponse->json('payGateTranID'),
            ]);

        if ($settlementResponse->status() != Response::HTTP_OK) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        return ($this->updateTransactionService)($transaction, [
            'status'       => Transaction::STATUS_SUCCESS,
            'reference_id' => $transactionResultResponse->json('payGateTranID'),
        ]);
    }
}
