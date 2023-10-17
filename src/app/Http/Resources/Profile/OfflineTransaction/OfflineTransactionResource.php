<?php

namespace App\Http\Resources\Profile\OfflineTransaction;

use App\Models\OfflineTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfflineTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var OfflineTransaction $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'paid_at' => $this->paid_at?->toDateTimeString(),
            'client_id' => $this->client_id,
            'invoice_id' => $this->invoice_id,
            '$this->transaction_id' => $this->transaction_id,
            'bank_account_id' => $this->bank_account_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'tracking_code' => $this->tracking_code,
            'mobile' => $this->mobile,
            'description' => $this->description,
        ];
    }
}
