<?php

namespace App\Http\Controllers\Profile;

use App\Actions\Profile\ListEverythingAction;
use App\Http\Requests\Profile\ListEverythingRequest;

class ListEverythingController
{
    private ListEverythingAction $listEverythingAction;

    public function __construct(ListEverythingAction $listEverythingAction)
    {
        $this->listEverythingAction = $listEverythingAction;
    }

    public function __invoke(ListEverythingRequest $request)
    {
        return ($this->listEverythingAction)($request->get('client_id'), $request->get('offset', 0));
    }
}
