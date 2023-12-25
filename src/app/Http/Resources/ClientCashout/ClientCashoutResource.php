<?php

namespace App\Http\Resources\ClientCashout;

use App\Http\Resources\ClientBankAccount\ClientBankAccountResource;
use App\Models\ClientCashout;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientCashoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ClientCashout $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'profile_id' => $this->profile_id,
            'client_bank_account' => ClientBankAccountResource::make($this->clientBankAccount),
            'zarinpal_payout_id' => $this->zarinpal_payout_id,
            'admin_id' => $this->admin_id,
            'amount' => $this->amount,
            'admin_note' => $this->admin_note,
            'status' => $this->status,
            'actions' => $this->actions,
            'rejected_by_bank' => $this->rejected_by_bank,
        ];
    }
}
