<?php

namespace App\Http\Controllers\Profile;

use App\Actions\Profile\ShowProfileIdAction;
use App\Http\Resources\Profile\ProfileResource;

class ShowProfileIdController
{
    public function __construct(private readonly ShowProfileIdAction $showProfileIdAction)
    {
    }

    public function __invoke(int $clientId)
    {
        return ProfileResource::make(($this->showProfileIdAction)($clientId));
    }
}
