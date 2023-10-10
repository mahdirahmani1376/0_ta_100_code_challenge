<?php

namespace App\Http\Requests\Admin\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_date' => 'nullable|date_format:"Y-m-d"',
            'due_date' => 'nullable|date_format:"Y-m-d"',
            'paid_at' => 'nullable|date_format:"Y-m-d"',
            'tax_rate' => 'nullable|integer|max:99|min:0',
            'invoice_number' => 'nullable|int',
            'fiscal_year'    => 'nullable|string',
        ];
    }
}
