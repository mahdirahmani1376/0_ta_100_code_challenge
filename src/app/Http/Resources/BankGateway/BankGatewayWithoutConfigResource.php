<?php

namespace App\Http\Resources\BankGateway;

use App\Models\BankGateway;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankGatewayWithoutConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var BankGateway $this */
        return [
            'id'                         => $this->id,
            'created_at'                 => $this->created_at?->toDateTimeString(),
            'updated_at'                 => $this->updated_at?->toDateTimeString(),
            'name'                       => $this->name,
            'name_fa'                    => $this->name_fa,
            'status'                     => $this->status,
            'order'                      => $this->order,
            'is_direct_payment_provider' => $this->is_direct_payment_provider,
        ];
    }
}
