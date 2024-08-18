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

class Asanpardakht extends BaseBankGateway implements Interface\BankGatewayInterface
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
            ->post($this->bankGateway->config['request_url'] . 'Token', [
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

        $transactionResultResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'usr'          => $this->bankGateway->config['username'],
            'pwd'          => $this->bankGateway->config['password']
        ])->get(
            $this->bankGateway->config['request_url'] . 'TranResult',
            [
                'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
                'localInvoiceId'          => $transaction->getKey(),
            ]
        );

        // "{\"cardNumber\":\"502229xxxx4837\",\"rrn\":\"055340189442\",\"refID\":\"177e41239094221f1\",\"amount\":\"3168000\",\"payGateTranID\":\"1938987738\",\"salesOrderID\":\"1094803\",\"hash\":null,\"serviceTypeId\":1,\"serviceStatusCode\":null,\"destinationMobile\":null,\"productId\":null,\"productNameFa\":null,\"productPrice\":null,\"operatorId\":null,\"operatorNameFa\":null,\"simTypeId\":null,\"simTypeTitleFa\":null,\"billId\":null,\"payId\":null,\"billOrganizationNameFa\":null,\"payGateTranDate\":\"2024-03-25T13:05:04.7777189\",\"payGateTranDateEpoch\":1711359304.0}",

        $verifyStatus = $transactionResultResponse->status();

        if ($verifyStatus != 200) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        $amount = $transactionResultResponse->json('amount');
        $transactionId = $transactionResultResponse->json('payGateTranID');
        if ($amount != $transaction->amount || $transactionId != $transaction->getKey()) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        $verifyResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'usr'          => $this->bankGateway->config['username'],
            'pwd'          => $this->bankGateway->config['password']
        ])->post($this->bankGateway->config['request_url'] . 'Verify', [
            'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
            'payGateTranId'           => $transactionId,
        ]);

        if ($verifyResponse->status() != 200) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        $settlementResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'usr'          => $this->bankGateway->config['username'],
            'pwd'          => $this->bankGateway->config['password']
        ])->post($this->bankGateway->config['request_url'] . 'Settlement', [
            'merchantConfigurationId' => $this->bankGateway->config['merchant_id'],
            'payGateTranId'           => $transactionId,
        ]);

        if ($settlementResponse->status() != 200) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        return ($this->updateTransactionService)($transaction, [
            'status'       => Transaction::STATUS_SUCCESS,
            'reference_id' => $transactionId,
        ]);
    }
}
