<?php

namespace App\Http\Resources\Wallet\CreditTransaction;

use App\Http\Resources\Invoice\ShowInvoiceResource;
use App\Models\CreditTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowCreditTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var CreditTransaction $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'profile_id' => $this->profile_id,
            'wallet_id' => $this->wallet_id,
            'invoice_id' => $this->invoice_id,
            'admin_id' => $this->admin_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'invoice' => $this->when($this->invoice, function () {
                return ShowInvoiceResource::make($this->invoice);
            })
        ];
    }
}
