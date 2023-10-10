<?php

namespace App\Http\Controllers\Profile;

use App\Actions\Profile\ListEverythingAction;
use App\Http\Requests\Profile\ListEverythingRequest;

class ListEverythingController
{
    public function __construct(private readonly ListEverythingAction $listEverythingAction)
    {
    }

    public function __invoke(ListEverythingRequest $request)
    {
        return ($this->listEverythingAction)($request->get('client_id'), $request->get('offset', 0));
    }
}
