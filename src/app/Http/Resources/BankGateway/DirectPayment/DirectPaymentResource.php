<?php

namespace App\Http\Resources\BankGateway\DirectPayment;

use App\Models\DirectPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DirectPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var DirectPayment $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'profile_id' => $this->profile_id,
            'status' => $this->status,
            'provider' => $this->provider,
            'config' => $this->config,
        ];
    }
}
