<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property $invoice_date
 * @property $due_date
 * @property $paid_at
 * @property $tax_rate
 * @property $invoice_number
 * @property $fiscal_year
 * @property $note
 * @property $source_invoice
 */
class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_date'   => ['nullable', 'date', 'date_format:Y-m-d'],
            'due_date'       => ['nullable', 'date', 'date_format:Y-m-d'],
            'paid_at'        => ['nullable', 'date', 'date_format:Y-m-d H:i:s'],
            'tax_rate'       => ['nullable', 'integer', 'max:99', 'min:0',],
            'invoice_number' => ['nullable', 'int',],
            'fiscal_year'    => ['nullable', 'string',],
            'note'           => ['nullable',],
            'source_invoice' => ['nullable', Rule::exists('invoices', 'id')],
            'created_at'     => ['nullable', 'date', 'date_format:Y-m-d H:i:s'],
            'processed_at'   => ['nullable', 'date', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
