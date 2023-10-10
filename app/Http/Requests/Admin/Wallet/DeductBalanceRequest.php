<?php

namespace App\Http\Requests\Admin\Wallet;

use App\Services\Wallet\FindWalletByClientIdService;
use Illuminate\Foundation\Http\FormRequest;

class DeductBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var FindWalletByClientIdService $findWalletByClientIdService */
        $findWalletByClientIdService = app(FindWalletByClientIdService::class);
        $wallet = $findWalletByClientIdService(request('clientId'));

        return $wallet->balance > request('amount');
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
