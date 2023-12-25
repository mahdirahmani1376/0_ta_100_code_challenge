<?php

namespace App\Http\Resources\Profile;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Profile $this */
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
        ];
    }
}
