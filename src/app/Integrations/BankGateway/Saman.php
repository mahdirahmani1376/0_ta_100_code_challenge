<?php

namespace App\Integrations\BankGateway;

use App\Exceptions\Http\BadRequestException;
use App\Integrations\BankGateway\Interface\BankGatewayInterface;
use App\Models\BankGateway;
use App\Models\Transaction;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Saman extends BaseBankGateway implements BankGatewayInterface
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
                'action'      => 'token',
                'TerminalId'  => $this->bankGateway->config['terminal_id'],
                'Amount'      => $transaction->amount,
                'ResNum'      => $transaction->getKey(),
                'RedirectUrl' => $callbackUrl
            ]);

        if ($response->json('status') != 1) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            return $this->getFailedRedirectUrl($transaction, $transaction->callback_url);
        }

        ($this->updateTransactionService)($transaction, ['tracking_code' => $response->json('token'),]);

        return Str::finish($this->bankGateway->config['start_url'], '?token=') . $response->json('token');
    }

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction
    {
        if ($data['state'] != 'OK') {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        $token = data_get($data, 'token');
        if ($data['ResNum'] != $transaction->getKey() || $token != $transaction->tracking_code) {
            Log::error('transaction possible fraud', [
                'gateway'     => 'saman',
                'transaction' => $transaction,
                'data'        => $data
            ]);
        }

        $response = Http::withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->post($this->bankGateway->config['verify_url'], [
                'RefNum'         => $transaction->tracking_code,
                'TerminalNumber' => $this->bankGateway->config['terminal_id'],
            ]);

        $amount = $response->json('TransactionDetail.OrginalAmount');

        if ($amount != $transaction->amount) {
            Log::error('transaction possible fraud', [
                'gateway'     => 'sadad',
                'transaction' => $transaction,
                'data'        => $amount
            ]);
        }


        if (!$response->json('Success') || $response->json('ResultCode') != 0) {
            ($this->updateTransactionService)($transaction, [
                'status' => Transaction::STATUS_FAIL,
            ]);
            throw new BadRequestException('Saman verify ResultCode: ' . $response->json('ResultCode'));
        }

        return ($this->updateTransactionService)($transaction, [
            'status'       => Transaction::STATUS_SUCCESS,
            'reference_id' => $response->json('TransactionDetail.RefNum'),
        ]);
    }
}
