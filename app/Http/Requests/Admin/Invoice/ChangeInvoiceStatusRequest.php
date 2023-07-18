<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeInvoiceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    Invoice::STATUS_UNPAID,
                    Invoice::STATUS_CANCELED,
                    Invoice::STATUS_DRAFT,
                    Invoice::STATUS_PAYMENT_PENDING,
                    Invoice::STATUS_COLLECTIONS,
                    Invoice::STATUS_PAID,
                ])
            ],
        ];
    }
}
