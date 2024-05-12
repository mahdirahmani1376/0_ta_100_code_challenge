<?php

namespace App\Integrations\BankGateway;

use App\Exceptions\Http\BadRequestException;
use App\Models\BankGateway;
use App\Models\Transaction;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Support\Facades\Http;

class OmidPay extends BaseBankGateway implements Interface\BankGatewayInterface
{
    private UpdateTransactionService $updateTransactionService;

    public function __construct(
        private readonly BankGateway $bankGateway,
    )
    {
        $this->updateTransactionService = app(UpdateTransactionService::class);
    }

    public static function make(BankGateway $bankGateway): Interface\BankGatewayInterface
    {
        return new static($bankGateway);
    }

    public function getRedirectUrlToGateway(Transaction $transaction, string $callbackUrl): string
    {
        $response = Http::withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->post($this->bankGateway->config['request_url'], [
                'WSContext'   => [
                    'UserId'   => $this->bankGateway->config['merchant_id'], // Yes MerchantId is the username as well
                    'Password' => $this->bankGateway->config['password'],
                ],
                'TransType'   => 'EN_GOODS',
                'ReserveNum'  => $transaction->id,
                'MerchantId'  => $this->bankGateway->config['merchant_id'],
                'TerminalId'  => $this->bankGateway->config['terminal_id'],
                'Amount'      => $transaction->amount,
                'RedirectUrl' => $callbackUrl
            ]);

        if ($response->failed() || is_null($response->json('Token'))) {
            return $this->getFailedRedirectUrl($transaction, $transaction->callback_url);
        }

        ($this->updateTransactionService)($transaction, ['tracking_code' => $response->json('Token'),]);

        return json_encode([
            'url'   => $this->bankGateway->config['start_url'],
            'token' => $response->json('Token'),
        ]);
    }

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction
    {
        if (!isset($data['State']) || $data['State'] != 'OK') {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }
        if (!isset($data['token']) || !isset($data['RefNum'])) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException("OmidPay callback missing RefNum and/or Token, transactionId: $transaction->id data:" . json_encode($data));
        }

        if ($data['token'] != $transaction->tracking_code) {
            ($this->updateTransactionService)($transaction, [
                'status' => Transaction::STATUS_FRAUD,
            ]);
            throw new BadRequestException("OmidPay token and transaction tracking_code mismatch");
        }

        $response = Http::withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->post($this->bankGateway->config['verify_url'], [
                'WSContext' => [
                    'UserId'   => $this->bankGateway->config['merchant_id'], // Yes MerchantId is the username as well
                    'Password' => $this->bankGateway->config['password'],
                ],
                'Token'     => $data['token'],
                'RefNum'    => $data['RefNum'],
            ]);

        if ($response->failed() || $response->json('Result') != 'erSucceed') {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        return ($this->updateTransactionService)($transaction, [
            'status'       => Transaction::STATUS_SUCCESS,
            'reference_id' => $data['RefNum'],
        ]);
    }
}
