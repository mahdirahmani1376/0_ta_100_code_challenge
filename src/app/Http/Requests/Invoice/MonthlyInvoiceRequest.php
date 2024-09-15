<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'profile_id'                     => 'required',
            'credit_transaction_description' => 'required',
            'credit_transaction_ids'         => 'required|array',
            'invoice_items'                  => 'required',
            'invoice_created_at'             => 'nullable',
            'invoice_due_date'               => 'nullable',
            'invoice_paid_date'              => 'nullable',
            'client_id' => ['required','numeric'],
        ];
    }
}
