<?php

namespace App\Http\Resources\Invoice;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MinimalInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Invoice $this */
        return [
            'id'              => $this->id,
            'invoice_id'      => $this->id,
            'created_at'      => self::formatDateTime($this->created_at),
            'updated_at'      => self::formatDateTime($this->updated_at),
            'due_date'        => self::formatDateTime($this->due_date),
            'paid_at'         => self::formatDateTime($this->paid_at),
            'profile_id'      => $this->profile_id,
            'rahkaran_id'     => $this->rahkaran_id,
            'payment_method'  => $this->payment_method,
            'balance'         => $this->balance,
            'total'           => $this->total,
            'sub_total'       => $this->sub_total,
            'tax_rate'        => $this->tax_rate,
            'tax'             => $this->tax,
            'status'          => $this->status,
            'is_mass_payment' => $this->is_mass_payment,
            'is_credit'       => $this->is_credit,
        ];
    }

    private static function formatDateTime(string|null $date)
    {
        return $date ? Carbon::create($date)->format('Y-m-d H:i:s') : null;
    }
}
