<?php

namespace App\Http\Resources\Admin\FinanceReport;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Invoice $this */

        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'due_date' => $this->due_date?->toDateTimeString(),
            'paid_at' => $this->paid_at?->toDateTimeString(),
            'client_id' => $this->client_id,
            'rahkaran_id' => $this->rahkaran_id,
            'payment_method' => $this->payment_method,
            'balance' => $this->balance,
            'total' => $this->total,
            'sub_total' => $this->sub_total,
            'tax_rate' => $this->tax_rate,
            'tax' => $this->tax,
            'status' => $this->status,
            'is_mass_payment' => $this->is_mass_payment,
            'admin_id' => $this->admin_id,
            'is_credit' => $this->is_credit,
        ];
    }
}
