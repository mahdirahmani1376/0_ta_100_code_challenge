<?php

namespace App\Http\Controllers\Invoice\MoadianLog;

use App\Actions\Invoice\MoadianLog\IndexMoadianLogAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\MoadianLog\IndexMoadianLogRequest;
use App\Http\Resources\Invoice\MoadianLog\MoadianLogResource;

class IndexMoadianLogController extends Controller
{
    public function __construct(private readonly IndexMoadianLogAction $indexMoadianLogAction)
    {
        parent::__construct();
    }

    public function __invoke(IndexMoadianLogRequest $request)
    {
        $result = ($this->indexMoadianLogAction)($request->validated());

        return MoadianLogResource::collection($result);
    }
}