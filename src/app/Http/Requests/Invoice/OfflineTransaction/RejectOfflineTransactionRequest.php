<?php

namespace App\Http\Requests\Invoice\OfflineTransaction;

use Illuminate\Foundation\Http\FormRequest;

class RejectOfflineTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_id' => ['required', 'integer',],
        ];
    }
}
