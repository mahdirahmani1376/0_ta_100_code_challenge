<?php

namespace App\Http\Controllers;

use App\Actions\ListEverythingAction;
use App\Http\Requests\ListEverythingRequest;

class ListEverythingController extends Controller
{
    public function __construct(private readonly ListEverythingAction $listEverythingAction)
    {
        parent::__construct();
    }

    public function __invoke(ListEverythingRequest $request)
    {
        return [
            'data' => ($this->listEverythingAction)(
                $request->get('profile_id'),
                $request->get('offset', 0),
                $request->get('per_page', 100),
            ),
            'per_page' => $request->get('per_page', 100)
        ];
    }
}
