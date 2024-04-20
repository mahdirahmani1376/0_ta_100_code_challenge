<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int invoice_id,
 * @property int source_invoice,
 */
class UpdateInvoiceSourceIdRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'invoice_id' => ['required','numeric',Rule::exists('invoices','id')],
            'source_invoice' => ['required']
        ];
    }
}
