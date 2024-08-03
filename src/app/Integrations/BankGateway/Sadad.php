<?php

namespace App\Integrations\BankGateway;

use App\Exceptions\Http\BadRequestException;
use App\Models\BankGateway;
use App\Models\Transaction;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Sadad extends BaseBankGateway implements Interface\BankGatewayInterface
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
            ->post($this->bankGateway->config['request_url'], [
                'TerminalId'    => $this->bankGateway->config['terminal_id'],
                'MerchantId'    => $this->bankGateway->config['merchant_id'],
                'Amount'        => $transaction->amount,
                'SignData'      => $this->encrypt("{$this->bankGateway->config['terminal_id']};$transaction->id;$transaction->amount", $this->bankGateway->config['api_key']),
                'ReturnUrl'     => $callbackUrl,
                'LocalDateTime' => now()->format("m/d/Y g:i:s a"),
                'OrderId'       => $transaction->id,
            ]);

        if ($response->json('ResCode') != 0) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            return $this->getFailedRedirectUrl($transaction, $transaction->callback_url);
        }

        ($this->updateTransactionService)($transaction, ['tracking_code' => $response->json('Token'),]);

        return Str::finish($this->bankGateway->config['start_url'], '?Token=') . $response->json('Token');
    }

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction
    {
        $this->callbackLog($transaction, $data);
        $resCode = $data['ResCode'] ?? null;
        $orderId = $data['OrderId'] ?? null;
        $token = $data['Token'] ?? null;
        if ($resCode != 0 || $orderId != $transaction->getKey()) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        if ($token != $transaction->tracking_code) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FRAUD,]);
        }

        $response = Http::withHeader('Content-Type', 'application/json')
            ->post($this->bankGateway->config['verify_url'], [
                'Token'    => $transaction->tracking_code,
                'SignData' => $this->encrypt($transaction->tracking_code, $this->bankGateway->config['api_key']),
            ]);

        Log::info('Sadad verify result', ['result' => $response->json()]);

        if (!$response->json('content.ResCode') != 0) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        /*if ($response->json('Amount') != $transaction->amount) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FRAUD,]);
        }*/

        return ($this->updateTransactionService)($transaction, [
            'status'       => Transaction::STATUS_SUCCESS,
            'reference_id' => $response->json('content.RetrivalRefNo'),
        ]);
    }

    private function encrypt($serializedData, $key): string
    {
        $key = base64_decode($key);
        $ciphertext = OpenSSL_encrypt($serializedData, "DES-EDE3", $key, OPENSSL_RAW_DATA);

        return base64_encode($ciphertext);
    }
}
