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
        return [
            'data' => ($this->listEverythingAction)(
                $request->get('profile_id'),
                $request->get('offset', 0),
                $request->get('perPage', 100),
            ),
            'perPage' => $request->get('perPage', 100)
        ];
    }
}
