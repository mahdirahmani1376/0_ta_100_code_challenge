<?php

namespace App\Http\Controllers\Profile;

use App\Actions\Profile\ShowProfileIdAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Profile\ProfileResource;

class ShowProfileIdController extends Controller
{
    public function __construct(private readonly ShowProfileIdAction $showProfileIdAction)
    {
        parent::__construct();
    }

    public function __invoke(int $clientId)
    {
        return ProfileResource::make(($this->showProfileIdAction)($clientId));
    }
}
