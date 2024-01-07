<?php

namespace App\Http\Controllers\Invoice\MoadianLog;

use App\Actions\Invoice\MoadianLog\IndexMoadianLogAction;
use App\Http\Requests\Invoice\MoadianLog\IndexMoadianLogRequest;
use App\Http\Resources\Invoice\MoadianLog\MoadianLogResource;

class IndexMoadianLogController
{
    public function __construct(private readonly IndexMoadianLogAction $indexMoadianLogAction)
    {
    }

    public function __invoke(IndexMoadianLogRequest $request)
    {
        $result = ($this->indexMoadianLogAction)($request->validated());

        return MoadianLogResource::collection($result);
    }
}