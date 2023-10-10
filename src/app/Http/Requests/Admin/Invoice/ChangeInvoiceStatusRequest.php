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
            'status' => ['required', Rule::in(self::availableStatus()),],
        ];
    }

    private function availableStatus(): array
    {
        switch (request('invoice')->status) {
            case Invoice::STATUS_DRAFT:
                return [
                    Invoice::STATUS_UNPAID,
                    Invoice::STATUS_CANCELED,
                ];
            case Invoice::STATUS_UNPAID:
                $statuses = [
                    Invoice::STATUS_CANCELED,
                    Invoice::STATUS_DRAFT,
                    Invoice::STATUS_PAYMENT_PENDING,
                    Invoice::STATUS_COLLECTIONS,
                ];
                if (request('invoice')->balance == 0) {
                    $statuses[] = Invoice::STATUS_PAID;
                }

                return $statuses;
            case Invoice::STATUS_CANCELED:
            case Invoice::STATUS_PAYMENT_PENDING:
            case Invoice::STATUS_COLLECTIONS:
                return [
                    Invoice::STATUS_UNPAID
                ];
            case Invoice::STATUS_REFUNDED:
            case Invoice::STATUS_PAID:
                return [];
        }
    }
}
