<?php

namespace App\Http\Requests\Admin\Wallet;

use App\Actions\Admin\Wallet\ShowWalletAction;
use App\Services\Wallet\FindWalletByProfileIdService;
use Illuminate\Foundation\Http\FormRequest;

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
            'date' => ['nullable', 'date_format:Y-m-d',],
        ];
    }
}
