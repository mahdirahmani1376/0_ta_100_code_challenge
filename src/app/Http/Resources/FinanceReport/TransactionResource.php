<?php

namespace App\Http\Resources\FinanceReport;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Transaction $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'profile_id' => $this->profile_id,
            'invoice_id' => $this->invoice_id,
            'rahkaran_id' => $this->rahkaran_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'description' => $this->description,
            'ip' => $this->ip,
            'tracking_code' => $this->tracking_code,
            'reference_id' => $this->reference_id,
            'invoice' => [
                'id' => $this->invoice->id,
                'profile_id' => $this->invoice->profile_id,
            ],
        ];
    }
}
