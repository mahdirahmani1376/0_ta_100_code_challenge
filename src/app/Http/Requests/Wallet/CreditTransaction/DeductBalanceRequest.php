<?php

namespace App\Http\Requests\Wallet\CreditTransaction;

use App\Actions\Wallet\ShowWalletAction;
use App\Services\Wallet\FindWalletByProfileIdService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeductBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var FindWalletByProfileIdService $showWalletAction */
        $showWalletAction = app(ShowWalletAction::class);
        $wallet = $showWalletAction(request('profileId'));

        return $wallet->balance >= request('amount');
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'gte:1',],
            'description' => ['nullable', 'string',],
            'date' => ['nullable', 'date',],
            'invoice_id' => ['nullable', 'integer', Rule::exists('invoices', 'id'),]
        ];
    }
}
