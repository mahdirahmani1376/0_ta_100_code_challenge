<?php

namespace App\Http\Requests\Admin\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class ChargeWalletInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric',],
            'profile_id' => ['required', 'numeric','exists:profiles,id',],
            'admin_id' => ['required', 'numeric',],
        ];
    }
}
