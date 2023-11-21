<?php

namespace App\Http\Requests\Profile\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class ShowWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
        ];
    }
}
