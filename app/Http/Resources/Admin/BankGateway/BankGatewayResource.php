<?php

namespace App\Http\Resources\Admin\BankGateway;

use App\Models\BankGateway;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankGatewayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var BankGateway $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'name' => $this->name,
            'name_fa' => $this->name_fa,
            'config' => $this->config,
        ];
    }
}
