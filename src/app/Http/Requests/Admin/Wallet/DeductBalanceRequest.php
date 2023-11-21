<?php

namespace App\Http\Requests\Admin\Wallet;

use App\Services\Wallet\FindWalletByProfileIdService;
use Illuminate\Foundation\Http\FormRequest;

class DeductBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var FindWalletByProfileIdService $findWalletByProfileIdService */
        $findWalletByProfileIdService = app(FindWalletByProfileIdService::class);
        $wallet = $findWalletByProfileIdService(request('profileId'));

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
