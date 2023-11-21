<?php

namespace App\Http\Controllers\Public\Profile;

use App\Actions\Public\Profile\ShowProfileIdAction;
use App\Http\Resources\Public\Profile\ProfileResource;

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
