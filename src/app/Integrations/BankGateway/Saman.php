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
        $this->callbackLog($transaction, $data);
        $state = $data['State'] ?? null;
        $refNumber = $data['RefNum'] ?? null;
        $successful = $state == 'OK';
        // refNu
        if (!$successful) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }

        if ($refNumber) {
            $transaction = ($this->updateTransactionService)($transaction, ['reference_id' => $refNumber,]);
        }

        $token = data_get($data, 'Token');
        $resNumber = $data['ResNum'] ?? null;
        if ($resNumber != $transaction->getKey() || $token != $transaction->tracking_code) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FRAUD,]);
        }

        $response = Http::withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->post($this->bankGateway->config['verify_url'], [
                'RefNum'         => $transaction->reference_id,
                'TerminalNumber' => $this->bankGateway->config['terminal_id'],
            ]);

        $amount = $response->json('TransactionDetail.OrginalAmount');

        if ($amount != $transaction->amount) {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FRAUD,]);
        }

        if (!$response->json('Success') || $response->json('ResultCode') != 0) {
            return ($this->updateTransactionService)($transaction, [
                'status' => Transaction::STATUS_FAIL,
            ]);
        }

        return ($this->updateTransactionService)($transaction, [
            'status'       => Transaction::STATUS_SUCCESS,
        ]);
    }
}
