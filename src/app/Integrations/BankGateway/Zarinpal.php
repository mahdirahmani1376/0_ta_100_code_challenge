<?php

namespace App\Integrations\BankGateway;

use App\Exceptions\Http\BadRequestException;
use App\Integrations\BankGateway\Interface\BankGatewayInterface;
use App\Models\BankGateway;
use App\Models\Transaction;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Zarinpal extends BaseBankGateway implements BankGatewayInterface
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
                'merchant_id'  => $this->bankGateway->config['merchant_id'],
                'amount'       => $transaction->amount,
                'callback_url' => $callbackUrl,
                'description'  => 'description', // TODO change this if needed
            ]);

        if ($response->json('data.code') != 100 || $response->json('data.code') != 101) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            return $this->getFailedRedirectUrl($transaction->invoice, $transaction->callback_url);
        }

        ($this->updateTransactionService)($transaction, ['tracking_code' => $response->json('data.authority'),]);

        return Str::finish($this->bankGateway->config['start_url'], '/') . $response->json('data.authority');
    }

    public function callbackFromGateway(Transaction $transaction, array $data): Transaction
    {
        if ($data['status'] == 'NOK') {
            return ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
        }
        if ($data['Authority'] != $transaction->tracking_code || $data['amount'] != $transaction->amount) {
            ($this->updateTransactionService)($transaction, [
                'status' => Transaction::STATUS_FRAUD,
            ]);
            throw new BadRequestException("Zarinpal miss match tracking_code, transactionId: $transaction->id , Authority: " . $data['Authority']);
        }

        $response = Http::withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->post($this->bankGateway->config['verify_url'], [
                'merchant_id' => $this->bankGateway->config['merchant_id'],
                'amount'      => $transaction->amount,
                'authority'   => $transaction->tracking_code,
            ]);

        if ($response->json('data.code') != 100 || $response->json('data.code') != 101) {
            ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_FAIL,]);
            throw new BadRequestException('Zarinpal verify code: ' . $response->json('data.code')); // TODO maybe use a custom exception class
        }

        return ($this->updateTransactionService)($transaction, [
            'status'       => Transaction::STATUS_SUCCESS,
            'reference_id' => $response->json('data.ref_id'),
        ]);
    }

    public static function createBankAccount($iban, $name)
    {
        // TODO legacy code from MainApp , needs refactor

        $url = config('services.zarinpal.next.url');
        $token = config('services.zarinpal.next.token');

        if (strlen($name) > 30) {
            $name = Str::substr($name, 0, 30);
        }

        $body = '{"query":"\\nmutation BankAccountAdd($iban:String!, $is_legal: Boolean!, $name:String!, $type: BankAccountTypeEnum) \\n{\\n    BankAccountAdd(iban:$iban, is_legal:$is_legal, name:$name, type:$type) {    \\n        id\\n        iban \\n        name\\n        status\\n        type\\n        is_legal\\n        holder_name\\n        issuing_bank {\\n            name\\n            slug\\n        } \\n        expired_at deleted_at\\n    }\\n} \\n",
                "variables":{"iban":"IR' . $iban . '","is_legal":false,"name":"' . $name . '","type":"SHARE"}}';

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headers,
        ));

        $response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $response = json_decode($response, 1);

        $errorMessage = data_get($response, 'errors.0.validation.0.message');

        if (!empty($errorMessage)) {
            throw match ($errorMessage) {
                'The provided iban is not valid.' => new BadRequestException(trans('validation.invalid_sheba')),
                'The iban has already been taken.' => new BadRequestException(trans('validation.sheba_already_taken')),
                default => new BadRequestException((string)$errorMessage),
            };
        }

        $data = data_get($response, 'data.BankAccountAdd');
        if (empty($data) || empty(data_get($data, 'id'))) {
            throw new BadRequestException(trans('validation.bank_account_zarinpal_fail'));
        }

        return data_get($data, 'id');
    }

    public static function cashoutToAccount($amount, $zarinpalBankAccountId)
    {
        // TODO legacy code from MainApp , needs refactor

        $url = config('services.zarinpal.next.url');
        $token = config('services.zarinpal.next.token');
        $terminalId = config('services.zarinpal.next.terminal_id');

        $body = '{"query":"mutation PayoutAdd($terminal_id: ID!,$bank_account_id: ID!,$amount: BigInteger!,$description: String,$reconciliation_parts: ReconciliationPartsEnum) \\n{\\n    PayoutAdd(terminal_id:$terminal_id,bank_account_id:$bank_account_id,amount:$amount,description:$description,reconciliation_parts:$reconciliation_parts)\\n    { \\n        reconciliation_parts\\n        id\\n        description\\n        terminal{ preferred_bank_account_id id }\\n        bank_account{ id iban holder_name issuing_bank{ slug name } } \\n        status\\n        amount\\n        percent\\n        created_at\\n        updated_at\\n    }\\n}\\n","variables":{"amount":"' . $amount . '","bank_account_id":"' . $zarinpalBankAccountId . '","description":"برگشت وجه سیستمی","reconciliation_parts":"SINGLE","terminal_id":"' . $terminalId . '"}}';

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headers
        ));

        $response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $response = json_decode($response, 1);

        $errorMessage = data_get($response, 'errors.0.validation.0.message');

        if (!empty($errorMessage))
            throw match ($errorMessage) {
                'The amount must be at least 100000.' => new BadRequestException(trans('validation.zarinpal_amount_be_least')),
                default => new BadRequestException((string)$errorMessage),
            };

        $errorMessage = data_get($response, 'errors.0.message');

        if (!empty($errorMessage)) {
            throw match ($errorMessage) {
                'bank account not active,this bank account status is not active' => new BadRequestException(trans('validation.zarinpal_bank_account_not_active')),
                default => new BadRequestException((string)$errorMessage),
            };
        }

        $data = data_get($response, 'data.PayoutAdd');
        if (empty($data) || empty(data_get($data, 'data.PayoutAdd')) || !isset($data['data']['PayoutAdd']['id'])) {
            throw new BadRequestException(trans('validation.cashout_zarinpal_fail'));
        }

        return $data['data']['PayoutAdd']['id'];
    }
}
