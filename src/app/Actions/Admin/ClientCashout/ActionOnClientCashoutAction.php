<?php

namespace App\Actions\Admin\ClientCashout;

use App\Actions\Admin\Wallet\DeductBalanceAction;
use App\Actions\Admin\Wallet\ShowWalletAction;
use App\Exceptions\Http\BadRequestException;
use App\Integrations\BankGateway\Zarinpal;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\Rahkaran\RahkaranService;
use App\Integrations\Rahkaran\ValueObjects\Payment;
use App\Integrations\Rahkaran\ValueObjects\PaymentDeposit;
use App\Models\AdminLog;
use App\Models\ClientBankAccount;
use App\Models\ClientCashout;
use App\Models\CreditTransaction;
use App\Services\Admin\ClientBankAccount\FindSimilarClientBankAccountWithZarinpalIdService;
use App\Services\Admin\ClientBankAccount\UpdateClientBankAccountService;
use App\Services\Admin\ClientCashout\UpdateClientCashoutService;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ActionOnClientCashoutAction
{
    public function __construct(
        private readonly UpdateClientBankAccountService                    $updateClientBankAccountService,
        private readonly UpdateClientCashoutService                        $updateClientCashoutService,
        private readonly ShowWalletAction                                  $showWalletAction,
        private readonly FindSimilarClientBankAccountWithZarinpalIdService $findSimilarClientBankAccountWithZarinpalIdService,
        private readonly DeductBalanceAction                               $deductBalanceAction,
    )
    {
    }

    public function __invoke(ClientCashout $clientCashout, string $action, array $data): ClientCashout
    {
        if (!in_array($action, $clientCashout->actions))
            throw new UnprocessableEntityHttpException(trans('validation.invalid_cashout_action'));

        if ($action === ClientCashout::ACTION_ACCEPT) {
            $this->acceptClientCashout($clientCashout, $data);
        } elseif ($action === ClientCashout::ACTION_REJECT) {
            ($this->updateClientCashoutService)($clientCashout, [
                'status' => ClientCashout::STATUS_REJECTED,
                'admin_note' => $data['admin_note'] ? $clientCashout->admin_note . ' --- ' . $data['admin_note'] : $clientCashout->admin_note,
                'admin_id' => $data['admin_id'],
            ]);
        } elseif ($action === ClientCashout::ACTION_REJECT_BANK) {
            ($this->updateClientBankAccountService)($clientCashout->clientBankAccount, ['status' => ClientBankAccount::STATUS_REJECTED]);
            ($this->updateClientCashoutService)($clientCashout, [
                'status' => ClientCashout::STATUS_REJECTED,
                'rejected_by_bank' => true,
                'admin_note' => $data['admin_note'] ? $clientCashout->admin_note . ' --- ' . $data['admin_note'] : $clientCashout->admin_note,
                'admin_id' => $data['admin_id'],
            ]);
        }

        admin_log(AdminLog::ACTION_ON_CASHOUT, $clientCashout, validatedData: $data);

        return $clientCashout;
    }

    public function acceptClientCashout(ClientCashout $clientCashout, array $data): void
    {
        $clientBankAccount = $clientCashout->clientBankAccount;
        if ($clientBankAccount->status != ClientBankAccount::STATUS_ACTIVE) {
            throw new BadRequestException(trans('validation.bank_account_not_active'));
        }

        $wallet = ($this->showWalletAction)($clientCashout->client_id);
        if ($clientCashout->amount > $wallet->balance) {
            throw new BadRequestException(trans('validation.not_enough_credit'));
        }
        if ($clientCashout->amount <= 0) {
            throw new BadRequestException(trans('validation.zarinpal_amount_be_least'));
        }

        // Make sure we have a zarinpal_bank_account_id either from similar ClientBankAccount record or fetch a new one via Zarinpal API
        if (is_null($clientBankAccount->zarinpal_bank_account_id)) {
            $similarClientBankAccount = ($this->findSimilarClientBankAccountWithZarinpalIdService)($clientBankAccount);
            if (!is_null($similarClientBankAccount)) {
                $clientBankAccount = ($this->updateClientBankAccountService)($clientBankAccount, [
                    'zarinpal_bank_account_id' => $similarClientBankAccount->zarinpal_bank_account_id,
                    'status' => ClientBankAccount::STATUS_ACTIVE,
                    'admin_id' => $data['admin_id'],
                ]);
            } else {
                $zarinpalId = Zarinpal::createBankAccount($clientBankAccount->sheba_number, $clientBankAccount->owner_name);
                $clientBankAccount = ($this->updateClientBankAccountService)($clientBankAccount, [
                    'zarinpal_bank_account_id' => $zarinpalId,
                    'status' => ClientBankAccount::STATUS_ACTIVE,
                    'admin_id' => $data['admin_id'],
                ]);
            }
        }

        if (is_null($clientCashout->zarinpal_payout_id)) {
            $payoutId = Zarinpal::cashoutToAccount($clientCashout->amount, $clientBankAccount->zarinpal_bank_account_id);
            ($this->updateClientCashoutService)($clientCashout, [
                'zarinpal_payout_id ' => $payoutId,
                'admin_id' => $data['admin_id'],
                'admin_note' => $data['admin_note'] ? $clientCashout->admin_note . ' --- ' . $data['admin_note'] : $clientCashout->admin_note,
            ]);
        }

        $creditTransaction = ($this->deductBalanceAction)($clientCashout->client_id, [
            'amount' => $clientCashout->amount * -1,
            'description' => 'بازگشت وجه به حساب بانکی کاربر - شماره درخواست : ' . $clientCashout->id,
        ]);

        /** @var RahkaranService $rahkaranService */
        $rahkaranService = app(RahkaranService::class);
        if (!$rahkaranService->isTestMode()) {
            $rahkaranService->createPayment($rahkaranService->getPaymentInstanceForCashout($creditTransaction));
        }

        ($this->updateClientCashoutService)($clientCashout, ['status' => ClientCashout::STATUS_ACTIVE]);
    }
}
